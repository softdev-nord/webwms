<?php

declare(strict_types=1);

namespace WebWMS\Controller;

use WebWMS\Entity\Feature;
use WebWMS\Entity\Group;
use WebWMS\Entity\Survey;
use WebWMS\Repository\FeatureRepository;
use WebWMS\Repository\GroupRepository;
use Doctrine\ORM\EntityNotFoundException;
use http\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @package:    WebWMS\Controller
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2022, SoftDev Nord
 * Class        FeatureController
 */
class FeatureController extends AbstractController
{
    public function __construct(
        private FeatureRepository $featureRepository,
        private GroupRepository $groupRepository
    ) {
    }

    #[Route('/feature/create', name: 'feature_create')]
    public function create(Request $request): Response
    {
        try {
            $name = $request->get('name') ?? "";
            $display_name = $request->get('display_name') ?? $name;
            if (empty($name)) {
                throw new \Symfony\Component\Config\Definition\Exception\Exception("Feature Name can not be null!");
            }

            $feature = new Feature();
            $featureName = "feature_" . $name;
            $feature->setName($featureName);
            $feature->setDisplayName($display_name);

            $feature->setDescription($request->get('description') ?? "Test Description");

            $featureId = $this->featureRepository->add($feature);
            return $this->json(['feature_id' => $featureId, "feature_name" => $featureName]);
        } catch (\Throwable $e) {
            return $this->json(["error_message" => $e->getMessage()]);
        }
    }


    /**
     * @param Group $group
     * @param Feature $feature
     *
     * @return Response
     */
    #[Route('/feature/{id}/group/{featureId}', name: 'assign-feature-to-group')]

    public function assign(Group $group, Feature $feature): Response
    {
        try {
            $group->addFeature($feature);

            $updatedGroup = $this->groupRepository->add($group);

            return $this->json([
              "groupId" => $updatedGroup,
              "message" => "Feature is been added to the group successfully",
            ]);
        } catch (\Throwable $e) {
            return $this->json(["error_message" => $e->getMessage()]);
        }
    }
}
