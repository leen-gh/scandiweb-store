
import { useState, useEffect } from 'react';

const useCart = () => {
    
  const [cartItems, setCartItems] = useState(() => {
    const storedCart = localStorage.getItem('cartItems');
    return storedCart ? JSON.parse(storedCart) : [];
  });

  useEffect(() => {
    localStorage.setItem('cartItems', JSON.stringify(cartItems));
  }, [cartItems]);

  const addToCart = (newItem) => {
  setCartItems((prevItems) => {
    const existingIndex = prevItems.findIndex((item) => {
      if (item.name !== newItem.name) return false;

      const existingAttributes = item.selectedAttributes;
      const newAttributes = newItem.selectedAttributes;
      const allKeys = Object.keys(existingAttributes);
      
      if (allKeys.length !== Object.keys(newAttributes).length) return false;


      return allKeys.every(
        key => existingAttributes[key] === newAttributes[key]
      );
    });

    if (existingIndex !== -1) {
      const updatedItems = [...prevItems];
      updatedItems[existingIndex] = {
        ...updatedItems[existingIndex],
        quantity: updatedItems[existingIndex].quantity + 1
      };
      return updatedItems;
    } else {
      return [...prevItems, { ...newItem, quantity: 1 }];
    }
  });
};

  const updateAttribute = (index, attrName, value) => {
    setCartItems(prevItems => {
      const updated = [...prevItems];
      const item = updated[index];

      updated[index] = {
        ...item,
        selectedAttributes: {
          ...item.selectedAttributes,
          [attrName]: value
        }
      };

      return updated;
    });
  };


  const increaseQuantity = (index) => {
    const updated = [...cartItems];
    updated[index].quantity += 1;
    setCartItems(updated);
  };

  const decreaseQuantity = (index) => {
    const updated = [...cartItems];
    if (updated[index].quantity > 1) {
      updated[index].quantity -= 1;
      setCartItems(updated);
    } else {
      updated.splice(index, 1);
      setCartItems(updated);
    }
  };

  const clearCart = () => {
    setCartItems([]);
  };

  return {
    cartItems,
    addToCart,
    increaseQuantity,
    decreaseQuantity,
    clearCart,
    updateAttribute
  };
};

export default useCart;
