<?php 
namespace pawcode\cart\tests;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use Codeception\Stub;
use pawcode\cart\tests\UnitTester;
use pawcode\cart\interfaces\CartableInterface;
use pawcode\cart\components\Cart;
use pawcode\cart\components\CartItem;

class CartItemCest
{
    public function _before(UnitTester $I)
    {
    }

    // tests
    public function tryToTest(UnitTester $I)
    {
    }

    public function testConstructor(UnitTester $I)
    {
        $cart = new Cart;
        $cartable = $this->getCartableInstance();

        // test cart config exception
        $I->expectException(
            new InvalidConfigException(
                Yii::t('app', 'Missing required config "cart"')
            ), 
            function () {
                $item = new CartItem;
            }
        );
        $I->expectException(
            new InvalidConfigException(
                Yii::t('app', 'Configuration "cart" must instance of {class}', [
                    'class' => Cart::class
                ])
            ), 
            function () {
                $item = new CartItem([
                    'cart' => 'testing'
                ]);
            }
        );

        // test cartable config exception
        $I->expectException(
            new InvalidConfigException(
                Yii::t('app', 'Missing required config "cartable"')
            ), 
            function () use ($cart) {
                $item = new CartItem(['cart' => $cart]);
            }
        );
        $I->expectException(
            new InvalidConfigException(
                Yii::t('app', 'Configuration "cartable" must instance of {class}', [
                    'class' => CartableInterface::class
                ])
            ), 
            function () use ($cart) {
                $item = new CartItem([
                    'cart' => $cart,
                    'cartable' => 'cartable'
                ]);
            }
        );
    }

    public function testConstructorByArrayConfig(UnitTester $I)
    {
        $cart = ['class' => Cart::class];

        $tempCartable = $this->getCartableInstance();
        $cartableClass = get_class($tempCartable);
        $cartable = ['class' => $cartableClass];

        $item = new CartItem([
            'cart' => $cart,
            'cartable' => $cartable,
        ]);

        $I->expectException(
            new InvalidConfigException(
                'Object configuration must be an array containing a "class" or "__class" element.'
            ), 
            function () use ($cart) {
                $item = new CartItem([
                    'cart' => ['makeIt' => 'fail'],
                    'cartable' => ['invalid' => 'cartableConifg'],
                ]);
            }
        );
    }

    public function testSetGetId(UnitTester $I)
    {
        $item = Stub::make(CartItem::class);
        $I->assertNull($item->getId());
        $I->assertNull($I->invokeProperty($item, '_id'));

        $item->setId('this is unique id');
        $I->assertEquals('this is unique id', $item->getId());
        $I->assertEquals('this is unique id', $I->invokeProperty($item, '_id'));
    }

    public function testSetGetCart(UnitTester $I)
    {
        $cart = new Cart;
        
        // create item without constructor
        $item = Stub::make(CartItem::class);

        $I->expectException(
            new InvalidConfigException(
                Yii::t('app', 'Configuration "cart" must instance of {class}', ['class' => Cart::class])
            ),
            function () use ($item) {
                $item->setCart('Invalid Cart');
            }
        );

        $I->expectException(
            new InvalidConfigException(
                'Object configuration must be an array containing a "class" or "__class" element.'
            ),
            function () use ($item) {
                $item->setCart(['Invalid Cart']);
            }
        );

        $I->expectException(
            new InvalidConfigException(
                Yii::t('app', 'Configuration "cart" must instance of {class}', ['class' => Cart::class])
            ),
            function () use ($item) {
                $item->setCart(['class' => Component::class]);
            }
        );

        $I->assertNull($item->getCart());

        $item->setCart(['class' => Cart::class]);
        $I->assertInstanceOf(Cart::class, $item->getCart());

        $item->setCart($cart);
        $I->assertEquals($cart, $item->getCart());
    }

    public function testSetGetCartable(UnitTester $I)
    {
        $cartable = $this->getCartableInstance();
        $cartableClass = get_class($cartable);
        
        // create item without constructor
        $item = Stub::make(CartItem::class);

        $I->expectException(
            new InvalidConfigException(
                Yii::t('app', 'Configuration "cartable" must instance of {class}', ['class' => CartableInterface::class])
            ),
            function () use ($item) {
                $item->setCartable('Invalid Cartable');
            }
        );

        $I->expectException(
            new InvalidConfigException(
                'Object configuration must be an array containing a "class" or "__class" element.'
            ),
            function () use ($item) {
                $item->setCartable(['Invalid Cartable']);
            }
        );

        $I->expectException(
            new InvalidConfigException(
                Yii::t('app', 'Configuration "cartable" must instance of {class}', ['class' => CartableInterface::class])
            ),
            function () use ($item) {
                $item->setCartable(['class' => Component::class]);
            }
        );

        $I->assertNull($item->getCartable());

        $item->setCartable(['class' => $cartableClass]);
        $I->assertInstanceOf($cartableClass, $item->getCartable());

        $item->setCartable($cartable);
        $I->assertEquals($cartable, $item->getCartable());
    }

    public function testGetName(UnitTester $I)
    {
        $cartable = $this->getCartableInstance();

        $item = Stub::make(CartItem::class);
        $I->invokeProperty($item, '_cartable', $cartable);
        $I->assertEquals('Cartable Name', $item->getName());
        
        $I->invokeProperty($cartable, '_name', 'Try to change cartable name');
        $I->assertEquals('Try to change cartable name', $item->getName());
    }

    public function testGetPrice(UnitTester $I)
    {
        $cartable = $this->getCartableInstance();

        $item = Stub::make(CartItem::class);
        $I->invokeProperty($item, '_cartable', $cartable);
        $I->assertEquals(1.50, $item->getPrice());
        
        $I->invokeProperty($cartable, '_price', 10.10);
        $I->assertEquals(10.10, $item->getPrice());
    }

    public function testGetTotal(UnitTester $I)
    {
        $cartable = $this->getCartableInstance();
        $I->invokeProperty($cartable, '_price', 3.50);

        $item = Stub::make(CartItem::class);
        $I->invokeProperty($item, '_cartable', $cartable);
        $I->invokeProperty($item, '_quantity', 1);

        $I->assertEquals(3.50, $item->getTotal());

        $I->invokeProperty($item, '_quantity', 5);
        $I->assertEquals(17.50, $item->getTotal());

        $I->invokeProperty($cartable, '_price', null);
        $I->assertEquals(0.00, $item->getTotal());

        $I->invokeProperty($cartable, '_price', 'not number at all');
        $I->assertEquals(0.00, $item->getTotal());
        
        $I->invokeProperty($cartable, '_price', 2.50);
        $I->invokeProperty($item, '_quantity', 2.6);
        $I->assertEquals(5.00, $item->getTotal());

        $I->invokeProperty($item, '_quantity', 2.4);
        $I->assertEquals(5.00, $item->getTotal());

        $I->invokeProperty($item, '_quantity', 'not number value');
        $I->assertEquals(0.00, $item->getTotal());

        $I->invokeProperty($cartable, '_price', 'not number at all');
        $I->invokeProperty($item, '_quantity', 'not number value');
        $I->assertEquals(0.00, $item->getTotal());
    }

    // functions
    protected function getCartableInstance($config = [])
    {
        return new class ($config) extends Component implements CartableInterface 
        {
            protected $_name = 'Cartable Name';
            protected $_price = 1.50;
            
            public function getId() 
            {
                return uniqid();
            }

            public function getName() 
            {
                return $this->_name;
            }

            public function getPrice() 
            {
                return $this->_price;
            }
        };
    }
}
