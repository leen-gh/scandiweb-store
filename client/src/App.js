import React, { useState } from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Header from './components/Header';
import ProductList from './components/ProductsList';
import CartOverlay from './components/CartOverlay';
import SingleProduct from './components/SingleProduct';
import useCart from './hooks/useCart';


function App() {
  const pathCategory = window.location.pathname.split("/")[1] || "all";
  const [selectedCategory, setSelectedCategory] = useState(pathCategory);
  const [cartOpen, setCartOpen] = useState(false);

  const {
    cartItems,
    addToCart,
    increaseQuantity,
    decreaseQuantity,
    clearCart
  } = useCart();

  return (
    <Router>
      <Header
        onCategoryChange={setSelectedCategory}
        onCartToggle={() => {setCartOpen(prev => !prev);}}
        cartItems={cartItems}
      />

      <CartOverlay
        isOpen={cartOpen}
        onClose={() => setCartOpen(false)}
        cartItems={cartItems}
        decreaseQuantity={decreaseQuantity}
        increaseQuantity={increaseQuantity}
        clearCart={clearCart}
      />

      <main className="p-6">
        <Routes>
          <Route
            path="/:category"
            element={
              <>
                <h3 className="text-3xl text-gray-700 capitalize">{selectedCategory}</h3>
                <ProductList category={selectedCategory} addTocart={addToCart} />
              </>
            }
          />
          <Route path="/product/:id" element={<SingleProduct addTocart={addToCart} openCart={() => setCartOpen(true)} />} />
        </Routes>
      </main>
    </Router>
  );
}

export default App;
