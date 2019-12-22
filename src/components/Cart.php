<?php
/**
 * PHP version 7.1
 * 
 * @author Mlax Wong <mlaxwong@gmail.com>
 */
namespace pawcode\cart\components;

use yii\base\Component;
use yii\helpers\ArrayHelper;
use pawcode\cart\interfaces\CartItemInterface;
use pawcode\cart\interfaces\CartableInterface;
use pawcode\cart\components\CartItem;

class Cart extends Component
{
    protected $_items = [];

    /**
     * Add cart item
     * - add new item variant to cart if not exists
     * - increate item variant quantity if exists
     * 
     * Below is an example:
     * ```php
     * $cart->addItem($cartable, 2, ['variant' => ['color' => 'red']])
     * ```
     * 
     * @param CartableInterface $cartable item object
     * @param integer           $quantity item quantity
     * @param array             $params   item configuration
     * 
     * @return CartItemInterface unique id for added item
     */
    public function addItem(CartableInterface $cartable, int $quantity = 1, array $params = [])
    {
        $variant = isset($params['variant']) ? $params['variant'] : [];
        
        if ($cartItem = $this->isItemVariantExists($cartable, $variant)) {
            $this->increaseItemQuantity($cartItem, $quantity);
        } else {
            $cartItem = $this->addItemByCartableVariant($cartable, $variant, $quantity);
        }

        return $cartItem;
    }

    /**
     * Add item variant to cart
     * 
     * @param CartableInterface $cartable cartable object
     * @param array             $variant  cartable variant
     * @param int               $quantity cart item quantity
     * 
     * @return CartItemInterface the newly added unique id string
     */
    protected function addItemByCartableVariant(CartableInterface $cartable, array $variant, int $quantity)
    {
        $uniqueId = $this->generateUniqueItemId();

        $cartItem = new CartItem(
            [
                'id' => $uniqueId,
                'cart' => $this,
                'cartable' => $cartable,
                'variant' => $variant,
                'quantity' => $quantity,
            ]
        );

        $this->_items[$uniqueId] = $cartItem;

        return $cartItem;
    }

    /**
     * Increate cart item variant quantity
     * 
     * @param CartItemInterface $cartItem unique id
     * @param integer           $quantity quantity
     * 
     * @return void
     */
    protected function increaseItemQuantity(CartItemInterface $cartItem, int $quantity)
    {
        if (isset($this->_items[$cartItem->getId()])) {
            $this->_items[$cartItem->getId()]->quantity += $quantity;
        }
    }
    
    /**
     * Remove cart item
     * 
     * Below is an example:
     * ```php
     * // remove by cartable type
     * $cart->removeItem($cartable);
     * 
     * // remove by cartable type specify variant
     * $cart->removeItem($cartable, ['color' => 'red']);
     * 
     * // remove by cart item unique id
     * $cart->removeItem('xasdr');
     * ```
     * 
     * @param string|CartableInterface|CartItemInterface $item    cart item, cartable or unique id
     * @param array                                      $variant variant
     * 
     * @return void
     */
    public function removeItem($item, array $variant = null)
    {
        if ($item instanceof CartItemInterface) { // if is cart item object
            $this->removeItemByCartItem($item);
        } else if ($item instanceof CartableInterface) { // if is cartable object
            if ($variant == null) {
                $this->removeItemByCartable($item);
            } else {
                $this->removeItemByVariant($item, $variant);
            }
        } else { // if is unique id
            $this->removeItemByUniqueId($item);
        }
    }

    /**
     * Remove cart item by unit id
     *
     * @param string $uniqueId item unique id
     * 
     * @return void
     */
    public function removeItemByUniqueId($uniqueId)
    {
        if (isset($this->_items[$uniqueId])) {
            unset($this->_items[$uniqueId]);
        }
    }

    /**
     * Remove cart item by cart item object
     *
     * @param CartItemInterface $item cart item object
     * 
     * @return void
     */
    public function removeItemByCartItem(CartItemInterface $item)
    {
        $this->removeItemByUniqueId($item->getId());
    }

    /**
     * Remove cart item by cartable
     *
     * @param CartableInterface $cartable cartable object
     * 
     * @return void
     */
    public function removeItemByCartable(CartableInterface $cartable)
    {
        $uniqueIds = $this->getUniqueIdsByCartable($cartable);
        foreach ($uniqueIds as $uniqueId) {
            $this->removeItemByUniqueId($uniqueId);
        }
    }

    /**
     * Remove cart item by cartable variant
     *
     * @param CartableInterface $cartable cartable object
     * @param array             $variant  cartable variant
     * 
     * @return void
     */
    public function removeItemByVariant(CartableInterface $cartable, array $variant)
    {
        if ($uniqueId = $this->getUniqueIdByVariant($cartable, $variant)) {
            $this->removeItemByUniqueId($uniqueId);
        }
    }

    /**
     * Get unique Ids by cartable
     *
     * @param CartableInterface $cartable cartable object
     * 
     * @return array item unique ids
     */
    public function getUniqueIdsByCartable(CartableInterface $cartable)
    {
        $uniqueIds = [];
        foreach ($this->_items as $uniqueId => $cartItem) {
            $cartItemCartable = $cartItem->getCartable();
            if ($cartItemCartable->getId() == $cartable->getId()) {
                $uniqueIds[] = $cartItem->getId();
            }
        }
        return $uniqueIds;
    }

    /**
     * Get unique id by variant
     *
     * @param CartableInterface $cartable cartable object
     * @param array             $variant  cartable variant
     * 
     * @return string|null item unique id or null 
     */
    public function getUniqueIdByVariant(CartableInterface $cartable, array $variant)
    {
        $cartItem = $this->isItemVariantExists($cartable, $variant);
        return $cartItem ? $cartItem->getId() : null;
    }

    /**
     * Check item variant exists
     * 
     * @param CartableInterface $cartable item object
     * @param array             $variant  item variant
     * 
     * @return CartItemInterface|false true if variant exists
     */
    protected function isItemVariantExists(CartableInterface $cartable, array $variant = [])
    {
        // sort variant by key
        ksort($variant);

        foreach ($this->_items as $cartItem) {
            $carItemCartable = $cartItem->getCartable();

            $cartItemVariant = $cartItem->getVariant();
            ksort($cartItemVariant);

            if ($carItemCartable->getId() == $cartable->getId() && $cartItemVariant == $variant) {
                return $cartItem;
            }
        }

        return false;
    }
    
    /**
     * Get all item ids
     *
     * @return array all items id
     */
    public function getItemIds()
    {
        return ArrayHelper::getColumn($this->_items, 'id');
    }

    /**
     * Generage unique item id
     *
     * @return string unique id
     */
    protected function generateUniqueItemId()
    {
        do {
            $uniqueId = uniqid();
        } while (in_array($uniqueId, $this->getItemIds()));
        return $uniqueId;
    }

    /**
     * Get total item count
     *
     * @return int totam item count
     */
    public function getItemCount()
    {
        $quantities = ArrayHelper::getColumn($this->_items, 'quantity');
        return (int) array_sum($quantities);
    }

    /**
     * Is cart empty?
     *
     * @return boolean true is cart empty
     */
    public function getIsEmpty()
    {
        return $this->getItemCount() == 0;
    }

    /**
     * Is cart not empty
     *
     * @return boolean true is cart is not empty
     */
    public function getIsNotEmpty()
    {
        return $this->getItemCount() > 0;
    }

    /**
     * Total cart amount
     *
     * @return float total cart amount
     */
    public function getTotal()
    {
        $totals = ArrayHelper::getColumn($this->_items, 'total');
        return (float) array_sum($totals);
    }

    /**
     * Clear cart
     *
     * @return void
     */
    public function clear()
    {
        $this->_items = [];
    }
}
