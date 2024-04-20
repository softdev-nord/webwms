<?php

declare(strict_types=1);

namespace WebWMS\Dto;

use WebWMS\Helper\HydrateStaticTrait;

/**
 * @package:    WebWMS\Dto
 * @author:     SoftDev Nord, Rene Irrgang
 * @copyright:  Copyright © 2019-2023, SoftDev Nord
 * Class        StockInFinalDto
 *
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 * @SuppressWarnings(PHPMD.CamelCaseVariableName)
 * @SuppressWarnings(PHPMD.CamelCaseParameterName)
 */
class StockInFinalDto
{
    use HydrateStaticTrait;

    public ?string $stock_su_id = null;

    public ?string $stock_system = null;

    public ?string $stock_ln = null;

    public ?string $stock_fb = null;

    public ?string $stock_sp = null;

    public ?string $stock_tf = null;

    public ?string $stock_quantity = null;

    public ?string $stock_tbe = null;

    public ?string $stock_coordinate = null;

    public ?string $charge = null;

    public ?string $article_nr = null;

    public ?string $article_id = null;

    public ?string $booking_method = null;

    public ?string $loading_equipment = null;

    public ?string $stock_location_id = null;

    public ?string $le_quantity = null;

    public function getStockSuId(): ?string
    {
        return $this->stock_su_id;
    }

    public function setStockSuId(?string $stock_su_id): self
    {
        $this->stock_su_id = $stock_su_id;

        return $this;
    }

    public function getStockSystem(): ?string
    {
        return $this->stock_system;
    }

    public function setStockSystem(?string $stock_system): self
    {
        $this->stock_system = $stock_system;

        return $this;
    }

    public function getStockLn(): ?string
    {
        return $this->stock_ln;
    }

    public function setStockLn(?string $stock_ln): self
    {
        $this->stock_ln = $stock_ln;

        return $this;
    }

    public function getStockFb(): ?string
    {
        return $this->stock_fb;
    }

    public function setStockFb(?string $stock_fb): self
    {
        $this->stock_fb = $stock_fb;

        return $this;
    }

    public function getStockSp(): ?string
    {
        return $this->stock_sp;
    }

    public function setStockSp(?string $stock_sp): self
    {
        $this->stock_sp = $stock_sp;

        return $this;
    }

    public function getStockTf(): ?string
    {
        return $this->stock_tf;
    }

    public function setStockTf(?string $stock_tf): self
    {
        $this->stock_tf = $stock_tf;

        return $this;
    }

    public function getStockQuantity(): ?string
    {
        return $this->stock_quantity;
    }

    public function setStockQuantity(?string $stock_quantity): self
    {
        $this->stock_quantity = $stock_quantity;

        return $this;
    }

    public function getStockTbe(): ?string
    {
        return $this->stock_tbe;
    }

    public function setStockTbe(?string $stock_tbe): self
    {
        $this->stock_tbe = $stock_tbe;

        return $this;
    }

    public function getStockCoordinate(): ?string
    {
        return $this->stock_coordinate;
    }

    public function setStockCoordinate(?string $stock_coordinate): self
    {
        $this->stock_coordinate = $stock_coordinate;

        return $this;
    }

    public function getCharge(): ?string
    {
        return $this->charge;
    }

    public function setCharge(?string $charge): self
    {
        $this->charge = $charge;

        return $this;
    }

    public function getArticleNr(): ?string
    {
        return $this->article_nr;
    }

    public function setArticleNr(?string $article_nr): self
    {
        $this->article_nr = $article_nr;

        return $this;
    }

    public function getArticleId(): ?string
    {
        return $this->article_id;
    }

    public function setArticleId(?string $article_id): self
    {
        $this->article_id = $article_id;

        return $this;
    }

    public function getBookingMethod(): ?string
    {
        return $this->booking_method;
    }

    public function setBookingMethod(?string $booking_method): self
    {
        $this->booking_method = $booking_method;

        return $this;
    }

    public function getLoadingEquipment(): ?string
    {
        return $this->loading_equipment;
    }

    public function setLoadingEquipment(?string $loading_equipment): self
    {
        $this->loading_equipment = $loading_equipment;

        return $this;
    }

    public function getStockLocationId(): ?string
    {
        return $this->stock_location_id;
    }

    public function setStockLocationId(?string $stock_location_id): self
    {
        $this->stock_location_id = $stock_location_id;

        return $this;
    }

    public function getLeQuantity(): ?string
    {
        return $this->le_quantity;
    }

    public function setLeQuantity(?string $le_quantity): self
    {
        $this->le_quantity = $le_quantity;

        return $this;
    }
}
