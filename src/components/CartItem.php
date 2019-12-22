<?php
/**
 * PHP version 7.1
 * 
 * @author Mlax Wong <mlaxwong@gmail.com>
 */
namespace pawcode\cart\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use pawcode\cart\components\Cart;
use pawcode\cart\interfaces\CartItemInterface;
use pawcode\cart\interfaces\CartableInterface;

class CartItem extends Component implements CartItemInterface
{
    /**
     * Cart item's unique id
     * 
     * @var string id
     */
    protected $_id;

    /**
     * Cart item's cart
     * 
     * @var Cart cart
     */
    protected $_cart;

    /**
     * Cart item's cartable
     *
     * @var CartableInterface cartable
     */
    protected $_cartable;

    /**
     * Cart item's variant
     *
     * @var array
     */
    protected $_variant = [];

    /**
     * Cart item's quantity
     *
     * @var integer
     */
    protected $_quantity = 1;

    /**
     * Constructor.
     * 
     * - Required cart config
     * - Required cartable config
     * 
     * @param array $config cart config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        if ($this->_cart === null) {
            throw new InvalidConfigException(Yii::t('app', 'Missing required config "cart"'));
        }

        if ($this->_cartable === null) {
            throw new InvalidConfigException(Yii::t('app', 'Missing required config "cartable"'));
        }
    }

    /**
     * Set cart
     *
     * @param array|Cart $cart cart item's cart
     * 
     * @return void
     */
    public function setCart($cart)
    {
        if (is_array($cart)) {
            $cart = Yii::createObject($cart);
        }

        if (!$cart instanceof Cart) {
            throw new InvalidConfigException(
                Yii::t('app', 'Configuration "cart" must instance of {class}', ['class' => Cart::class])
            );
        }

        $this->_cart = $cart;
    }

    /**
     * Set cartable
     *
     * @param array|CartableInterface $cartable cartable item's cartable
     * 
     * @return void
     */
    public function setCartable($cartable)
    {
        if (is_array($cartable)) {
            $cartable = Yii::createObject($cartable);
        }

        if (!$cartable instanceof CartableInterface) {
            throw new InvalidConfigException(
                Yii::t(
                    'app', 
                    'Configuration "cartable" must instance of {class}', 
                    ['class' => CartableInterface::class]
                )
            );
        }
        
        $this->_cartable = $cartable;
    }

    /**
     * Get cartable
     *
     * @return CartableInterface|null
     */
    public function getCartable()
    {
        return $this->_cartable;
    }

    /**
     * Get cart
     *
     * @return Cart|null
     */
    public function getCart()
    {
        return $this->_cart;
    }

    /**
     * Set cart item id
     *
     * @param string $id cart item id
     * 
     * @return void
     */
    public function setId($id)
    {
        $this->_id = $id;
    }
    
    /**
     * Get cart item id
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Cart item name
     *
     * @return string|null Cart item name
     */
    public function getName()
    {
        $cartable = $this->getCartable();
        return $cartable === null ? null : $cartable->getName();
    }

    /**
     * Cart item price
     *
     * @return float Cart item price
     */
    public function getPrice()
    {
        $cartable = $this->getCartable();
        return $cartable === null ? 0.00 : $cartable->getPrice();
    }

    /**
     * Set cart item variant
     *
     * @param array $variant cart item variant
     * 
     * @return void
     */
    public function setVariant(array $variant)
    {
        $this->_variant = $variant;
    }

    /**
     * Get cart item variant
     *
     * @return array
     */
    public function getVariant()
    {
        return $this->_variant;
    }

    /**
     * Set cart item quantity
     *
     * @param integer $quantity cart item quantity
     * 
     * @return void
     */
    public function setQuantity(int $quantity)
    {
        $this->_quantity = $quantity;
    }

    /**
     * Get cart item quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->_quantity;
    }

    /**
     * Get total price after quantity
     *
     * @return float total price
     */
    public function getTotal()
    {
        return (float) $this->getPrice() * (int) $this->getQuantity();
    }
}
