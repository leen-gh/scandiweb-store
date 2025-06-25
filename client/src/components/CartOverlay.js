import React from 'react';
import { useMutation } from '@apollo/client';
import { PLACE_ORDER } from '../graphQl/queries';


export default function CartOverlay({ isOpen, onClose, cartItems, decreaseQuantity, increaseQuantity, clearCart}) {
  const total = cartItems.reduce((total, item) => {
    return total + item.price * item.quantity;
  }, 0);

  const [placeOrder] = useMutation(PLACE_ORDER);

  const handlePlaceOrder = async () => {
    try {
      const { data } = await placeOrder({
        variables: {
          items: cartItems.map(item => ({
            name: item.name,
            price: item.price,
            quantity: item.quantity,
            selectedAttributes: JSON.stringify(item.selectedAttributes),
          })),
          totalPrice: total,
        },
      });

      console.log('Order placed:', data.placeOrder);
      localStorage.removeItem('cartItems');
      clearCart();
      onClose();
      alert('Order placed successfully!');
    } catch (error) {
      console.error('Order failed:', error);
      alert('Order failed. Please try again.');
    }
  };

  return (
    <div
      data-testid="cart-overlay"
      onClick={onClose}
      className={`
      fixed inset-0 top-16 bg-black bg-opacity-50 flex justify-end z-50 overflow-y-auto
      transition-opacity duration-300 ease-in-out
      ${isOpen ? 'opacity-100' : 'opacity-0 pointer-events-none hidden'}
      `}
    >
      <div
        onClick={(e) => e.stopPropagation()}
        className={`
          bg-white w-96 h-fit p-6 relative overflow-y-auto
          transform transition-transform duration-300 ease-in-out
          ${isOpen ? 'translate-x-0' : 'translate-x-full'}
        `}
      >


        <h2 className="text-xl font-bold mb-4">My Bag: <span className="font-light text-lg">{cartItems.length} Items</span></h2>

        {cartItems.length === 0 ? (
          <p>Your cart is empty.</p>
        ) : (
          <ul className="space-y-6">
            {cartItems.map((item, index) => (
              <li key={index} className="flex gap-4 items-center justify-between">
                <div className="p-4 border-b">
                  <h3 className="text-grey-700 font-light">{item.name}</h3>
                  <p className="text-base font-medium">${item.price}</p>
                  {item.attributes.map(attr => {
                    const attrName = attr.name.toLowerCase().replace(/\s+/g, '-');
                    return (
                      <div key={attr.name} data-testid={`cart-item-attribute-${attrName}`}>
                        <p className="text-sm font-medium">{attr.name}:</p>
                        <div className="flex gap-2 mt-1">
                          {attr.options.map((option, i) => {
                            const optionValue = option.toLowerCase().replace(/\s+/g, '-');
                            const isSelected = item.selectedAttributes[attr.name] === option;
                              return (
                                <span
                                  key={option}
                                  className={`border rounded transition ${
                                    isSelected ? 'border-2 border-green-400' : 'border-gray-300'
                                  } ${attr.name.toLowerCase() === 'color' ? 'px-4 py-4' : 'p-1'}`}
                                  style={attr.name.toLowerCase() === 'color' ? { backgroundColor: option } : {}}
                                  data-testid={`cart-item-attribute-${attrName}-${optionValue}${isSelected ? '-selected' : ''}`}
                                >
                                  {attr.name.toLowerCase() === 'color' ? '' : option}
                                </span>
                              );
                          })}
                        </div>
                      </div>
                    );
                  })}
                  <div className="flex items-center gap-2 mt-4">
                    <button
                      onClick={() => increaseQuantity(index, item.quantity)}
                      data-testid="cart-item-amount-increase"
                      className="px-2 py-1 border"
                    >
                      +
                    </button>
                    <span data-testid="cart-item-amount">{item.quantity}</span>
                    <button
                      onClick={() => decreaseQuantity(index, item.quantity)}
                      data-testid="cart-item-amount-decrease"
                      className="px-2 py-1 border"
                    >
                      -
                    </button>
                  </div>
                </div>
                <img
                  src={item.image}
                  alt={item.name}
                  className="w-24 h-32 object-cover border"
                />
              </li>
            ))}
          </ul>
        )}

        {cartItems.length > 0 && (
          <div className="mt-6 border-t pt-4">
            <div className="p-4 text-right font-bold text-lg">
              Total: <span data-testid="cart-total">${total.toFixed(2)}</span>
            </div>
            <button
              onClick={handlePlaceOrder}
              data-testid="place-order"
              disabled={cartItems.length === 0}
              className={`mt-4 w-full text-white py-2 rounded transition ${
                cartItems.length === 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700'
              }`}
            >
              Place Order
            </button>
          </div>
        )}
      </div>
    </div>
  );
}
