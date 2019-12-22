<?php
/**
 * PHP version 7.1
 * 
 * @author Mlax Wong <mlaxwong@gmail.com>
 */
namespace pawcode\cart\interfaces;

interface CartableInterface
{
    /**
     * Ger cartable id
     *
     * @return mixed
     */
    public function getId();

    /**
     * Ger cartable name
     *
     * @return string
     */
    public function getName();

    /**
     * Get cartable price
     *
     * @return float
     */
    public function getPrice();
}
