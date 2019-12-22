<?php
/**
 * PHP version 7.1
 * 
 * @author Mlax Wong <mlaxwong@gmail.com>
 */
namespace pawcode\cart\interfaces;

interface CartItemInterface
{
    /**
     * Set cart item id
     *
     * @param string $id cart item id
     * 
     * @return void
     */
    public function setId($id);

    /**
     * Get cart item id
     *
     * @return string
     */
    public function getId();

    /**
     * Get cart item name
     *
     * @return string
     */
    public function getName();

    /**
     * Get cart item price
     *
     * @return float
     */
    public function getPrice();

    /**
     * Set cart item variant
     *
     * @param array $variant cart item variant
     * 
     * @return void
     */
    public function setVariant(array $variant);

    /**
     * Get cart item variant
     *
     * @return array
     */
    public function getVariant();

    /**
     * Set cart item quantity
     *
     * @param int $quantity cart item quantity
     * 
     * @return void
     */
    public function setQuantity(int $quantity);

    /**
     * Get cart item quantity
     *
     * @return int
     */
    public function getQuantity();

    /**
     * Get total price after quantity
     *
     * @return float total price
     */
    public function getTotal();
}
