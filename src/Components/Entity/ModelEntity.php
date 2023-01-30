<?php

namespace WebWMS\Components\Entity;

use Doctrine\Common\Collections\ArrayCollection;

abstract class ModelEntity
{
    /**
     * Example:.
     *
     * $model->fromArray($data);
     * $model->setShipping($shippingModel->fromArray($shippingData));
     *
     * @param array<string, ModelEntity> $array
     */
    public function fromArray(array $array = [], array $fillable = []): ModelEntity
    {
        foreach ($array as $key => $value) {
            if (count($fillable) && !in_array($key, $fillable)) {
                continue;
            }

            $method = 'set'.ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     *
     * Helper function to set the association data of a ORM\OneToOne association of doctrine.
     * <br><br>
     * The <b>$data</b> parameter contains the data for the property. It can contains an array with model data
     * or and instance of the expected model. If the $data parameter is set to null the associated model
     * will removed.
     * <br><br>
     * The <b>$model</b> parameter expects the full name of the associated model.
     *
     * @param ModelEntity|ArrayCollection|array|null $data
     *
     * @return $this
     */
    public function setOneToOne($data, string $model, string $property, string $reference = null): ModelEntity
    {
        $getterFunction = 'get'.ucfirst($property);
        $setterFunction = (null !== $reference) ? 'set'.ucfirst($reference) : false;

        $this->$getterFunction();

        // If an expected instance passed, set this in the internal property
        if ($data instanceof $model) {
            $this->$property = $data;
            if ($setterFunction) {
                $this->$property->$setterFunction($this);
            }

            return $this;
        }

        // Check if expected model already exists but null passed, than clear the association.
        if (null === $data && $this->$getterFunction()) {
            if ($setterFunction) {
                $this->$property->$setterFunction(null);
            }
            $this->$property = null;

            return $this;
        }

        // If the parameter is no array, return
        if (!is_array($data) || empty($data)) {
            return $this;
        }

        // Check if the model association isn't created
        if (null === $this->$getterFunction()) {
            $this->$property = new $model();
        }

        // Load array data into the object and set association reference.
        $this->$property->fromArray($data);
        if ($setterFunction) {
            $this->$property->$setterFunction($this);
        }

        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ElseExpression)
     *
     * Helper function to set the association data of a ORM\OneToMany association of doctrine.
     * <br><br>
     * The <b>$data</b> parameter contains the data for the collection property. It can contains an array of
     * models or data arrays. If the $data parameter is set to null the associated collection will cleared.
     *
     * @param array[]|ModelEntity[]|null $data
     *
     * @return $this
     */
    public function setOneToMany(?array $data, string $model, string $property, string $reference = null): ModelEntity
    {
        $getterFunction = 'get'.ucfirst($property);
        $setterFunction = null;
        if (null !== $reference) {
            $setterFunction = 'set'.ucfirst($reference);
        }

        // To remove the whole one to many association, u can pass null as parameter.
        if (null === $data) {
            $this->$getterFunction()->clear();

            return $this;
        }
        // If no array passed or if false passed, return
        if (!is_array($data)) {
            return $this;
        }

        // Create a new collection to collect all updated and created models.
        $updated = new ArrayCollection();

        // Iterate all passed items
        /** @var array[]|ModelEntity[]|ArrayCollection<ModelEntity> $data */
        foreach ($data as $item) {
            // To get the right collection item use the internal helper function
            if (is_array($item) && isset($item['id']) && null !== $item['id']) {
                $attribute = $this->getArrayCollectionElementById($this->$getterFunction(), $item['id']);
                if (!$attribute instanceof $model) {
                    $attribute = new $model();
                }
            // If the item is an array without an id, create a new model.
            } elseif (is_array($item)) {
                $attribute = new $model();
            // If the item is no array, it could be an instance of the expected object.
            } else {
                $attribute = $item;
            }

            // Check if the object correctly initialed. If this is not the case continue.
            if (!$attribute instanceof $model) {
                continue;
            }

            // If the current item is an array, use the from array function to set the data.
            if (is_array($item)) {
                $attribute->fromArray($item);
            }

            // After the attribute filled with data, set the association reference and add the model to the internal collection.
            if (null !== $setterFunction) {
                $attribute->$setterFunction($this);
            }

            if (!$this->$getterFunction()->contains($attribute)) {
                $this->$getterFunction()->add($attribute);
            }

            // Add the model to the updated collection to have an flag which models updated.
            $updated->add($attribute);
        }

        // After all passed data items added to the internal collection, we have to iterate the items
        // to remove all old items which are not updated.
        foreach ($this->$getterFunction() as $attr) {
            // The updated collection contains all updated and created models.
            if (null !== $updated->contains($attr)) {
                $this->$getterFunction()->removeElement($attr);
            }
        }

        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     * Helper function to set the association data of a ORM\ManyToOne association of doctrine.
     * <br><br>
     * The <b>$data</b> parameter contains the data for the collection property. It can contains an array of
     * models or data arrays. If the $data parameter is set to null the associated collection will cleared.
     * <br><br>
     * The <b>$model</b> parameter expects the full name of the associated model.
     *
     * @param ModelEntity|array|null $data
     *
     * @return $this
     *
     *@throws \InvalidArgumentException
     */
    public function setManyToOne($data, string $model, string $property): ModelEntity
    {
        $getterFunction = 'get'.ucfirst($property);
        $this->$getterFunction();

        // If an expected instance passed, set this in the internal property
        if ($data instanceof $model) {
            $this->$property = $data;

            return $this;
        }

        // Check if expected model already exists but null passed, than clear the association.
        if (null === $data && $this->$getterFunction()) {
            $this->$property = null;

            return $this;
        }

        // If the parameter is no array, return
        if (!is_array($data) || empty($data)) {
            return $this;
        }

        // Check if the model association isn't created
        $instance = $this->$getterFunction();
        if (null === $instance) {
            $instance = new $model();
        }

        $id = $instance->getId();

        // If an id passed, the already assigned model has an id and the ids are not equal, we can't update the model instance.
        // Otherwise we would update the instance with the id 1 with the data for the instance with id 2.
        if (!empty($data['id']) && !empty($id) && $data['id'] !== $id) {
            throw new \InvalidArgumentException('Passed id and id of the already assigned model are not equal');
        }

        $instance->fromArray($data);
        $this->$property = $instance;

        return $this;
    }

    /**
     * @param ArrayCollection<ModelEntity>|ModelEntity[] $collection
     */
    private function getArrayCollectionElementById(array|ArrayCollection $collection, int $id): ?ModelEntity
    {
        if (0 === $collection->count()) {
            return null;
        }

        foreach ($collection as $item) {
            if ($item['id'] === $id) {
                return $item;
            }
        }

        return null;
    }
}
