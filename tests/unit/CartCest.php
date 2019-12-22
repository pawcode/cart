<?php 
namespace pawcode\cart\tests;

use yii\base\Component;
use pawcode\cart\tests\UnitTester;
use pawcode\cart\interfaces\CartableInterface;
use pawcode\cart\interfaces\CartItemInterface;
use pawcode\cart\components\Cart;
use pawcode\cart\components\CartItem;

class CartCest
{
    public function _before(UnitTester $I)
    {
    }

    // tests
    public function tryToTest(UnitTester $I)
    {
        
    }

    public function testIsItemVariantExists(UnitTester $I)
    {
        $cart = new Cart;

        // sample cartable
        $apple = $this->getCartableInstance([
            'id' => 1,
            'name' => 'Apple',
            'price' => 1.5,
        ]);
        $orange = $this->getCartableInstance([
            'id' => 2,
            'name' => 'Orange',
            'price' => 1.5,
        ]);

        // test on empty cart
        $I->assertFalse($I->invokeMethod($cart, 'isItemVariantExists', [$apple]));
        $I->assertFalse($I->invokeMethod($cart, 'isItemVariantExists', [$apple, ['color' => 'red']]));
        $I->assertFalse($I->invokeMethod($cart, 'isItemVariantExists', [$orange]));

        // test without variant
        $cartItem1 = new \pawcode\cart\components\CartItem([
            'id' => uniqid(),
            'cart' => $cart,
            'cartable' => $apple,
        ]);
        $I->invokeProperty($cart, '_items', [$cartItem1]);
        $I->assertEquals($cartItem1, $I->invokeMethod($cart, 'isItemVariantExists', [$apple]));
        $I->assertFalse($I->invokeMethod($cart, 'isItemVariantExists', [
            $apple,
            ['color' => 'red'],
        ]));

        // test with variant
        $cartItem2 = new \pawcode\cart\components\CartItem([
            'id' => uniqid(),
            'cart' => $cart,
            'cartable' => $apple,
            'variant' => [
                'color' => 'red',
            ]
        ]);
        $I->invokeProperty($cart, '_items', [$cartItem2]);
        $I->assertEquals($cartItem2, $I->invokeMethod($cart, 'isItemVariantExists', [
            $apple,
            ['color' => 'red'],
        ]));
        $I->assertFalse($I->invokeMethod($cart, 'isItemVariantExists', [$apple]));
        $I->assertFalse($I->invokeMethod($cart, 'isItemVariantExists', [$orange]));

        // test with multiple variant with random data sort
        $cartItem3 = new \pawcode\cart\components\CartItem([
            'id' => uniqid(),
            'cart' => $cart,
            'cartable' => $apple,
            'variant' => [
                'size' => 'xl',
                'vatamin' => 'c',
                'color' => 'red',
            ]
        ]);
        $cartItem4 = new \pawcode\cart\components\CartItem([
            'id' => uniqid(),
            'cart' => $cart,
            'cartable' => $apple,
            'variant' => []
        ]);
        $cartItem5 = new \pawcode\cart\components\CartItem([
            'id' => uniqid(),
            'cart' => $cart,
            'cartable' => $orange,
            'variant' => [
                'color' => 'orange',
            ]
        ]);
        $I->invokeProperty($cart, '_items', [$cartItem3, $cartItem4, $cartItem5]);
        $I->assertEquals($cartItem3, $I->invokeMethod($cart, 'isItemVariantExists', [
            $apple,
            [
                'size' => 'xl',
                'color' => 'red',
                'vatamin' => 'c',
            ],
        ]));
        $I->assertEquals($cartItem4, $I->invokeMethod($cart, 'isItemVariantExists', [$apple]));
        $I->assertFalse($I->invokeMethod($cart, 'isItemVariantExists', [$orange]));
        $I->assertFalse($I->invokeMethod($cart, 'isItemVariantExists', [$orange, ['color' => 'red']]));
        $I->assertEquals($cartItem5, $I->invokeMethod($cart, 'isItemVariantExists', [$orange, ['color' => 'orange']]));
    }

    public function testAddItem(UnitTester $I)
    {
        $cart = new Cart;

        // create test case cartables
        $apple = $this->getCartableInstance([
            'id' => 1,
            'name' => 'Apple',
            'price' => 1.5,
        ]);
        $orange = $this->getCartableInstance([
            'id' => 2,
            'name' => 'Orange',
            'price' => 1,
        ]);

        // test add add a item
        $cartItem1 = $cart->addItem($apple);
        $I->assertInstanceOf(CartItemInterface::class, $cartItem1);
        $I->assertEquals(
            [
                $cartItem1->id => new CartItem([
                    'id' => $cartItem1->id,
                    'cart' => $cart,
                    'cartable' => $apple,
                    'quantity' => 1,
                    'variant' => [],
                ])
            ], 
            $I->invokeProperty($cart, '_items')
        );

        // test add different item type
        $cartItem2 = $cart->addItem($orange);
        $I->assertInstanceOf(CartItemInterface::class, $cartItem2);
        $I->assertEquals(
            [
                $cartItem1->id => new CartItem([
                    'id' => $cartItem1->id,
                    'cart' => $cart,
                    'cartable' => $apple,
                    'quantity' => 1,
                    'variant' => [],
                ]),
                $cartItem2->id => new CartItem([
                    'id' => $cartItem2->id,
                    'cart' => $cart,
                    'cartable' => $orange,
                    'quantity' => 1,
                    'variant' => [],
                ]),
            ], 
            $I->invokeProperty($cart, '_items')
        );

        // test add existing item type
        $I->assertInstanceOf(CartItemInterface::class, $cart->addItem($apple));
        $I->assertEquals(
            [
                $cartItem1->id => new CartItem([
                    'id' => $cartItem1->id,
                    'cart' => $cart,
                    'cartable' => $apple,
                    'quantity' => 2,
                    'variant' => [],
                ]),
                $cartItem2->id => new CartItem([
                    'id' => $cartItem2->id,
                    'cart' => $cart,
                    'cartable' => $orange,
                    'quantity' => 1,
                    'variant' => [],
                ]),
            ], 
            $I->invokeProperty($cart, '_items')
        );
    }

    public function testGetUniqueIdsByCartable(UnitTester $I)
    {
        $cart = new Cart;

         // create test case cartables
         $apple = $this->getCartableInstance([
            'id' => 1,
            'name' => 'Apple',
            'price' => 1.5,
        ]);
        $orange = $this->getCartableInstance([
            'id' => 2,
            'name' => 'Orange',
            'price' => 1,
        ]);
        $banana = $this->getCartableInstance([
            'id' => 3,
            'name' => 'Banana',
            'price' => 0.8,
        ]);

        $cartItem1 = $cart->addItem($apple);
        $cartItem2 = $cart->addItem($apple, 1, [
            'variant' => [
                'storage' => 256,
            ],
        ]);
        $cartItem3 = $cart->addItem($apple, 1, [
            'variant' => [
                'storage' => 64,
            ],
        ]);
        $cartItem4 = $cart->addItem($orange);
        $cartItem5 = $cart->addItem($apple, 1, [
            'variant' => [
                'storage' => 516,
            ],
        ]);

        $I->assertEquals([$cartItem1->getId(), $cartItem2->getId(), $cartItem3->getId(), $cartItem5->getId()], $cart->getUniqueIdsByCartable($apple));
        $I->assertEquals([$cartItem4->getId()], $cart->getUniqueIdsByCartable($orange));
        $I->assertEquals([], $cart->getUniqueIdsByCartable($banana));
    }

    public function testGetUniqueIdByVariant(UnitTester $I)
    {
        $cart = new Cart;

        // create test case cartables
        $apple = $this->getCartableInstance([
            'id' => 1,
            'name' => 'Apple',
            'price' => 1.5,
        ]);
        $orange = $this->getCartableInstance([
            'id' => 2,
            'name' => 'Orange',
            'price' => 1,
        ]);

        $cartItem1 = $cart->addItem($apple);
        $cartItem2 = $cart->addItem($apple, 1, [
            'variant' => [
                'color' => 'red'
            ],
        ]);
        $cartItem3 = $cart->addItem($apple, 1, [
            'variant' => [
                'color' => 'green'
            ],
        ]);
        $cartItem4 = $cart->addItem($orange, 1, [
            'variant' => [
                'size' => 'XL'
            ],
        ]);

        $I->assertEquals($cartItem4->getId(), $cart->getUniqueIdByVariant($orange, ['size' => 'XL']));
        $I->assertEquals($cartItem3->getId(), $cart->getUniqueIdByVariant($apple, ['color' => 'green']));
        $I->assertEquals($cartItem2->getId(), $cart->getUniqueIdByVariant($apple, ['color' => 'red']));
        $I->assertNull($cart->getUniqueIdByVariant($apple, ['color' => 'blue']));
    }

    public function testRemoveItemByUniqueId(UnitTester $I)
    {
        $cart = new Cart;

        // create test case cartables
        $apple = $this->getCartableInstance([
            'id' => 1,
            'name' => 'Apple',
            'price' => 1.5,
        ]);
        $orange = $this->getCartableInstance([
            'id' => 2,
            'name' => 'Orange',
            'price' => 1,
        ]);

        // add sample data
        $cartItem1 = $cart->addItem($apple);
        $cartItem2 = $cart->addItem($orange);

        // check cart items
        $I->assertEquals(
            [
                $cartItem1->getId() => new CartItem([
                    'id' => $cartItem1->getId(),
                    'cart' => $cart,
                    'cartable' => $apple,
                    'quantity' => 1,
                    'variant' => [],
                ]),
                $cartItem2->getId() => new CartItem([
                    'id' => $cartItem2->getId(),
                    'cart' => $cart,
                    'cartable' => $orange,
                    'quantity' => 1,
                    'variant' => [],
                ]),
            ], 
            $I->invokeProperty($cart, '_items')
        );

        // test remove item by unique id
        $cart->removeItemByUniqueId($cartItem2->getId());
        $I->assertEquals(
            [
                $cartItem1->getId() => new CartItem([
                    'id' => $cartItem1->getId(),
                    'cart' => $cart,
                    'cartable' => $apple,
                    'quantity' => 1,
                    'variant' => [],
                ]),
            ], 
            $I->invokeProperty($cart, '_items')
        );

        $cart->removeItemByUniqueId($cartItem1->getId());
        $I->assertEquals([], $I->invokeProperty($cart, '_items'));
    }

    public function testRemoveItemByCartItem(UnitTester $I)
    {
        $cart = new Cart;

        // create test case cartables
        $apple = $this->getCartableInstance([
            'id' => 1,
            'name' => 'Apple',
            'price' => 1.5,
        ]);
        $orange = $this->getCartableInstance([
            'id' => 2,
            'name' => 'Orange',
            'price' => 1,
        ]);

        $cartItem1 = $cart->addItem($apple);

        $cartItem2 = $cart->addItem($orange);

        $I->assertEquals(
            [
                $cartItem1->getId() => new CartItem([
                    'id' => $cartItem1->getId(),
                    'cart' => $cart,
                    'cartable' => $apple,
                    'quantity' => 1,
                    'variant' => [],
                ]),
                $cartItem2->getId() => new CartItem([
                    'id' => $cartItem2->getId(),
                    'cart' => $cart,
                    'cartable' => $orange,
                    'quantity' => 1,
                    'variant' => [],
                ]),
            ], 
            $I->invokeProperty($cart, '_items')
        );

        $cart->removeItemByCartItem($cartItem1);
        $I->assertEquals(
            [
                $cartItem2->getId() => new CartItem([
                    'id' => $cartItem2->getId(),
                    'cart' => $cart,
                    'cartable' => $orange,
                    'quantity' => 1,
                    'variant' => [],
                ]),
            ], 
            $I->invokeProperty($cart, '_items')
        );

        $cart->removeItemByCartItem($cartItem2);
        $I->assertEquals([], $I->invokeProperty($cart, '_items'));
    }

    public function testRemoveItemByCartable(UnitTester $I)
    {
        $cart = new Cart;

        // create test case cartables
        $apple = $this->getCartableInstance([
            'id' => 1,
            'name' => 'Apple',
            'price' => 1.5,
        ]);
        $orange = $this->getCartableInstance([
            'id' => 2,
            'name' => 'Orange',
            'price' => 1,
        ]);

        $cartItem1 = $cart->addItem($apple);

        $cartItem2 = $cart->addItem($orange);

        $I->assertEquals(
            [
                $cartItem1->getId() => new CartItem([
                    'id' => $cartItem1->getId(),
                    'cart' => $cart,
                    'cartable' => $apple,
                    'quantity' => 1,
                    'variant' => [],
                ]),
                $cartItem2->getId() => new CartItem([
                    'id' => $cartItem2->getId(),
                    'cart' => $cart,
                    'cartable' => $orange,
                    'quantity' => 1,
                    'variant' => [],
                ]),
            ], 
            $I->invokeProperty($cart, '_items')
        );

        $cart->removeItemByCartable($apple);
        $I->assertEquals(
            [
                $cartItem2->getId() => new CartItem([
                    'id' => $cartItem2->getId(),
                    'cart' => $cart,
                    'cartable' => $orange,
                    'quantity' => 1,
                    'variant' => [],
                ]),
            ], 
            $I->invokeProperty($cart, '_items')
        );

        $cart->removeItemByCartable($orange);
        $I->assertEquals([], $I->invokeProperty($cart, '_items'));
    }

    public function testRemoveItemByVariant(UnitTester $I)
    {
        $cart = new Cart;

        // create test case cartables
        $apple = $this->getCartableInstance([
            'id' => 1,
            'name' => 'Apple Smartphone',
            'price' => 5000,
        ]);
        $huawei = $this->getCartableInstance([
            'id' => 2,
            'name' => 'Huawei P20',
            'price' => 4500,
        ]);

        // add sample variant to cart
        $cartItem1 = $cart->addItem($apple, 1, [
            'variant' => [
                'storage' => 256
            ],
        ]);
        $cartItem2 = $cart->addItem($apple, 1, [
            'variant' => [
                'storage' => 128
            ],
        ]);
        $cartItem3 = $cart->addItem($huawei, 1, [
            'variant' => [
                'color' => 'white'
            ],
        ]);
        $cartItem4 = $cart->addItem($huawei, 1, [
            'variant' => [
                'color' => 'red'
            ],
        ]);

        // check cart items
        $I->assertEquals(
            [
                $cartItem1->getId() => new CartItem([
                    'id' => $cartItem1->getId(),
                    'cart' => $cart,
                    'cartable' => $apple,
                    'quantity' => 1,
                    'variant' => [
                        'storage' => 256,
                    ],
                ]),
                $cartItem2->getId() => new CartItem([
                    'id' => $cartItem2->getId(),
                    'cart' => $cart,
                    'cartable' => $apple,
                    'quantity' => 1,
                    'variant' => [
                        'storage' => 128,
                    ],
                ]),
                $cartItem3->getId() => new CartItem([
                    'id' => $cartItem3->getId(),
                    'cart' => $cart,
                    'cartable' => $huawei,
                    'quantity' => 1,
                    'variant' => [
                        'color' => 'white',
                    ],
                ]),
                $cartItem4->getId() => new CartItem([
                    'id' => $cartItem4->getId(),
                    'cart' => $cart,
                    'cartable' => $huawei,
                    'quantity' => 1,
                    'variant' => [
                        'color' => 'red',
                    ],
                ]),
            ], 
            $I->invokeProperty($cart, '_items')
        );

        // try to remove item
        $cart->removeItemByVariant($huawei, ['color' => 'white']);
        $I->assertEquals(
            [
                $cartItem1->getId() => new CartItem([
                    'id' => $cartItem1->getId(),
                    'cart' => $cart,
                    'cartable' => $apple,
                    'quantity' => 1,
                    'variant' => [
                        'storage' => 256,
                    ],
                ]),
                $cartItem2->getId() => new CartItem([
                    'id' => $cartItem2->getId(),
                    'cart' => $cart,
                    'cartable' => $apple,
                    'quantity' => 1,
                    'variant' => [
                        'storage' => 128,
                    ],
                ]),
                $cartItem4->getId() => new CartItem([
                    'id' => $cartItem4->getId(),
                    'cart' => $cart,
                    'cartable' => $huawei,
                    'quantity' => 1,
                    'variant' => [
                        'color' => 'red',
                    ],
                ]),
            ], 
            $I->invokeProperty($cart, '_items')
        );

        $cart->removeItemByVariant($apple, ['storage' => 256]);
        $I->assertEquals(
            [
                $cartItem2->getId() => new CartItem([
                    'id' => $cartItem2->getId(),
                    'cart' => $cart,
                    'cartable' => $apple,
                    'quantity' => 1,
                    'variant' => [
                        'storage' => 128,
                    ],
                ]),
                $cartItem4->getId() => new CartItem([
                    'id' => $cartItem4->getId(),
                    'cart' => $cart,
                    'cartable' => $huawei,
                    'quantity' => 1,
                    'variant' => [
                        'color' => 'red',
                    ],
                ]),
            ], 
            $I->invokeProperty($cart, '_items')
        );

        $cart->removeItemByVariant($apple, ['storage' => 128]);
        $I->assertEquals(
            [
                $cartItem4->getId() => new CartItem([
                    'id' => $cartItem4->getId(),
                    'cart' => $cart,
                    'cartable' => $huawei,
                    'quantity' => 1,
                    'variant' => [
                        'color' => 'red',
                    ],
                ]),
            ], 
            $I->invokeProperty($cart, '_items')
        );

        $cart->removeItemByVariant($huawei, ['color' => 'red']);
        $I->assertEquals([], $I->invokeProperty($cart, '_items'));
    }

    public function testRemoveItem(UnitTester $I)
    {
        $cart = new Cart;

        // create test case cartables
        $apple = $this->getCartableInstance([
            'id' => 1,
            'name' => 'Apple Smartphone',
            'price' => 5000,
        ]);
        $huawei = $this->getCartableInstance([
            'id' => 2,
            'name' => 'Huawei P20',
            'price' => 4500,
        ]);

        // add different type item
        $cartItem1 = $cart->addItem($apple);
        $cartItem2 = $cart->addItem($apple, 1, [
            'variant' => [
                'storage' => 64,
            ],
        ]);
        $cartItem3 = $cart->addItem($huawei);

        // check cart items
        $I->assertEquals(
            [
                $cartItem1->getId() => new CartItem([
                    'id' => $cartItem1->getId(),
                    'cart' => $cart,
                    'cartable' => $apple,
                    'quantity' => 1,
                    'variant' => [],
                ]),
                $cartItem2->getId() => new CartItem([
                    'id' => $cartItem2->getId(),
                    'cart' => $cart,
                    'cartable' => $apple,
                    'quantity' => 1,
                    'variant' => [
                        'storage' => 64
                    ],
                ]),
                $cartItem3->getId() => new CartItem([
                    'id' => $cartItem3->getId(),
                    'cart' => $cart,
                    'cartable' => $huawei,
                    'quantity' => 1,
                    'variant' => [],
                ])
            ], 
            $I->invokeProperty($cart, '_items')
        );

        // test remove item by cartable type
        $cart->removeItem($apple);
        $I->assertEquals(
            [
                $cartItem3->getId() => new CartItem([
                    'id' => $cartItem3->getId(),
                    'cart' => $cart,
                    'cartable' => $huawei,
                    'quantity' => 1,
                    'variant' => [],
                ])
            ], 
            $I->invokeProperty($cart, '_items')
        );

        // add more example
        $cartItem4 = $cart->addItem($apple, 1, [
            'variant' => [
                'storage' => 256,
            ],
        ]);
        $I->assertEquals(
            [
                $cartItem3->getId() => new CartItem([
                    'id' => $cartItem3->getId(),
                    'cart' => $cart,
                    'cartable' => $huawei,
                    'quantity' => 1,
                    'variant' => [],
                ]),
                $cartItem4->getId() => new CartItem([
                    'id' => $cartItem4->getId(),
                    'cart' => $cart,
                    'cartable' => $apple,
                    'quantity' => 1,
                    'variant' => [
                        'storage' => 256,
                    ],
                ]),
            ], 
            $I->invokeProperty($cart, '_items')
        );

        // test remove by unique id
        $cart->removeItem($cartItem3->getId());
        $I->assertEquals(
            [
                $cartItem4->getId() => new CartItem([
                    'id' => $cartItem4->getId(),
                    'cart' => $cart,
                    'cartable' => $apple,
                    'quantity' => 1,
                    'variant' => [
                        'storage' => 256,
                    ],
                ]),
            ], 
            $I->invokeProperty($cart, '_items')
        );

        // add another example
        $cartItem5 = $cart->addItem($apple, 1, [
            'variant' => [
                'storage' => 64,
            ],
        ]);
        $I->assertEquals(
            [
                $cartItem4->getId() => new CartItem([
                    'id' => $cartItem4->getId(),
                    'cart' => $cart,
                    'cartable' => $apple,
                    'quantity' => 1,
                    'variant' => [
                        'storage' => 256,
                    ],
                ]),
                $cartItem5->getId() => new CartItem([
                    'id' => $cartItem5->getId(),
                    'cart' => $cart,
                    'cartable' => $apple,
                    'quantity' => 1,
                    'variant' => [
                        'storage' => 64,
                    ],
                ]),
            ], 
            $I->invokeProperty($cart, '_items')
        );

        // remove by varaints
        $cart->removeItem($apple, ['storage' => 256]);
        $I->assertEquals(
            [
                $cartItem5->getId() => new CartItem([
                    'id' => $cartItem5->getId(),
                    'cart' => $cart,
                    'cartable' => $apple,
                    'quantity' => 1,
                    'variant' => [
                        'storage' => 64,
                    ],
                ]),
            ], 
            $I->invokeProperty($cart, '_items')
        );

        $cart->removeItem($apple, ['storage' => 64]);
        $I->assertEquals([], $I->invokeProperty($cart, '_items'));

        // add another example
        $cartItem6 = $cart->addItem($huawei, 1, [
            'variant' => [
                'color' => 'red',
            ],
        ]);
        $I->assertEquals(
            [
                $cartItem6->getId() => new CartItem([
                    'id' => $cartItem6->getId(),
                    'cart' => $cart,
                    'cartable' => $huawei,
                    'quantity' => 1,
                    'variant' => [
                        'color' => 'red',
                    ],
                ]),
            ], 
            $I->invokeProperty($cart, '_items')
        );
        $cart->removeItem($cartItem6);
        $I->assertEquals([], $I->invokeProperty($cart, '_items'));
    }

    public function testGetItemIds($I)
    {
        $cart = new Cart;
        $I->invokeProperty($cart, '_items', [
            ['id' => '1lj23l1j2l3'],
            ['id' => 'ansdforelws'],
            ['id' => '0gh5jd7f7fnt'],
        ]);
        $I->assertEquals(['1lj23l1j2l3', 'ansdforelws', '0gh5jd7f7fnt'], $cart->getItemIds());
    }

    public function testGenerateUniqueItemId($I)
    {
        $cart = new Cart;

        // generate 1000 unique ids
        $ids = [];
        $items = [];
        for ($i = 1; $i < 1000; $i++) {
            do {
                $uniqueId = uniqid();
            } while (in_array($uniqueId, $ids));
            $ids[] = $uniqueId;
            $items[] = ['id' => $uniqueId];
        }

        $I->assertNotContains($I->invokeMethod($cart, 'generateUniqueItemId'), $ids);
    }

    public function testGetItemCount(UnitTester $I)
    {
        $cart = new Cart;

        // create test case cartables
        $apple = $this->getCartableInstance([
            'id' => 1,
            'name' => 'Apple Smartphone',
            'price' => 5000,
        ]);
        $huawei = $this->getCartableInstance([
            'id' => 2,
            'name' => 'Huawei P20',
            'price' => 4500,
        ]);

        // empty cart
        $I->assertEquals(0, $cart->getItemCount());

        $I->invokeProperty($cart, '_items', [
            $uniqueId1 = uniqid() => new CartItem([
                'id' => $uniqueId1,
                'cart' => $cart,
                'cartable' => $apple,
                'quantity' => 3,
                'variant' => [
                    'storage' => 128,
                ],
            ]),
            $uniqueId2 = uniqid() => new CartItem([
                'id' => $uniqueId2 = uniqid(),
                'cart' => $cart,
                'cartable' => $huawei,
                'quantity' => 2,
                'variant' => [
                    'color' => 'red',
                ],
            ]),
        ]);

        $I->assertEquals(5, $cart->getItemCount());
    }

    public function testGetItemCountByAddAndRemoveItem(UnitTester $I)
    {
        $cart = new Cart;

        // create test case cartables
        $apple = $this->getCartableInstance([
            'id' => 1,
            'name' => 'Apple Smartphone',
            'price' => 5000,
        ]);
        $huawei = $this->getCartableInstance([
            'id' => 2,
            'name' => 'Huawei P20',
            'price' => 4500,
        ]);

        // empty cart
        $I->assertEquals(0, $cart->getItemCount());
        
        $cart->addItem($apple);
        $I->assertEquals(1, $cart->getItemCount());
        
        $cart->removeItem($apple);
        $I->assertEquals(0, $cart->getItemCount());

        $cart->addItem($apple, 5);
        $cart->addItem($huawei);
        $I->assertEquals(6, $cart->getItemCount());

        $cart->addItem($huawei, 1, [
            'variant' => [
                'color' => 'red'
            ]
        ]);
        $I->assertEquals(7, $cart->getItemCount());

        $cart->addItem($apple, 5, [
            'variant' => [
                'storage' => 256
            ]
        ]);
        $I->assertEquals(12, $cart->getItemCount());
    }

    public function testGetIsEmpty(UnitTester $I)
    {
        $cart = new Cart;

        // create test case cartables
        $apple = $this->getCartableInstance([
            'id' => 1,
            'name' => 'Apple Smartphone',
            'price' => 5000,
        ]);
        $huawei = $this->getCartableInstance([
            'id' => 2,
            'name' => 'Huawei P20',
            'price' => 4500,
        ]);

        $I->assertTrue($cart->getIsEmpty());
        
        $cart->addItem($apple);
        $I->assertFalse($cart->getIsEmpty());

        $cart->addItem($huawei);
        $I->assertFalse($cart->getIsEmpty());
        
        $cart->removeItem($apple);
        $I->assertFalse($cart->getIsEmpty());

        $cart->removeItem($huawei);
        $I->assertTrue($cart->getIsEmpty());
    }

    public function testGetIsNotEmpty(UnitTester $I)
    {
        $cart = new Cart;

        // create test case cartables
        $apple = $this->getCartableInstance([
            'id' => 1,
            'name' => 'Apple Smartphone',
            'price' => 5000,
        ]);
        $huawei = $this->getCartableInstance([
            'id' => 2,
            'name' => 'Huawei P20',
            'price' => 4500,
        ]);

        $I->assertFalse($cart->getIsNotEmpty());
        
        $cart->addItem($apple);
        $I->assertTrue($cart->getIsNotEmpty());

        $cart->addItem($huawei);
        $I->assertTrue($cart->getIsNotEmpty());
        
        $cart->removeItem($apple);
        $I->assertTrue($cart->getIsNotEmpty());

        $cart->removeItem($huawei);
        $I->assertFalse($cart->getIsNotEmpty());
    }

    public function testGetTotal(UnitTester $I)
    {
        $cart = new Cart;

        // create test case cartables
        $apple = $this->getCartableInstance([
            'id' => 1,
            'name' => 'Apple Smartphone',
            'price' => 5000,
        ]);
        $huawei = $this->getCartableInstance([
            'id' => 2,
            'name' => 'Huawei P20',
            'price' => 4500,
        ]);

        $I->invokeProperty($cart, '_items', [
            $uniqueId1 = uniqid() => new CartItem([
                'id' => $uniqueId1,
                'cart' => $cart,
                'cartable' => $apple,
                'quantity' => 3,
                'variant' => [
                    'storage' => 128,
                ],
            ]),
            $uniqueId2 = uniqid() => new CartItem([
                'id' => $uniqueId2 = uniqid(),
                'cart' => $cart,
                'cartable' => $huawei,
                'quantity' => 2,
                'variant' => [
                    'color' => 'red',
                ],
            ]),
        ]);

        $I->assertEquals(24000, $cart->getTotal());
    }

    public function testClear(UnitTester $I)
    {
        $cart = new Cart;

        // create test case cartables
        $apple = $this->getCartableInstance([
            'id' => 1,
            'name' => 'Apple Smartphone',
            'price' => 5000,
        ]);
        $huawei = $this->getCartableInstance([
            'id' => 2,
            'name' => 'Huawei P20',
            'price' => 4500,
        ]);

        $I->invokeProperty($cart, '_items', [
            $uniqueId1 = uniqid() => new CartItem([
                'id' => $uniqueId1,
                'cart' => $cart,
                'cartable' => $apple,
                'quantity' => 3,
                'variant' => [
                    'storage' => 128,
                ],
            ]),
            $uniqueId2 = uniqid() => new CartItem([
                'id' => $uniqueId2 = uniqid(),
                'cart' => $cart,
                'cartable' => $huawei,
                'quantity' => 2,
                'variant' => [
                    'color' => 'red',
                ],
            ]),
        ]);

        $cart->clear();
        $I->assertEquals([], $I->invokeProperty($cart, '_items'));
    }

    // functions
    protected function getCartableInstance($config)
    {
        return new class ($config) extends Component implements CartableInterface
        {
            protected $_id;
            protected $_name;
            protected $_price;
            protected $_variant;

            public function getId()
            {
                return $this->_id;
            }
            public function setId($id)
            {
                $this->_id = $id;
            }

            public function getName()
            {
                return $this->_name;
            }
            public function setName($name)
            {
                $this->_name = $name;
            }

            public function getPrice()
            {
                return $this->_price;
            }
            public function setPrice($price)
            {
                $this->_price = $price;
            }

            public function getVariant()
            {
                return $this->_variant;
            }
            public function setVariant($variant)
            {
                $this->_variant = $variant;
            }
        };
    }
}
