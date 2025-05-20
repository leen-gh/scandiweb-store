import React, { useEffect } from 'react';
import { useQuery } from '@apollo/client';
import { GET_CATEGORIES } from '../graphQl/queries';
import logo from '../Images/logo.png';
import { Link } from 'react-router-dom';

export default function Header({ onCategoryChange, cartItems = [], onCartToggle , category}) {
  const { loading, error, data } = useQuery(GET_CATEGORIES);
  

  useEffect(() => {
  console.log("activeCategory:", category);
  }, [category]);

  if (loading) return <p>Loading...</p>;
  if (error) {
    console.error('GraphQL Error:', error);
    return <p>Error Loading Categories</p>
  }

  const totalQuantity = cartItems.reduce((acc, item) => acc + item.quantity, 0);

  const handleClick = (name) => {
    onCategoryChange(name);
  };

  return (
    <header className="p-4 flex justify-between items-center">
      <div className="flex gap-4">
        {data.categories?.map(({ name }) => (
          <Link
          to={`/${name.toLowerCase()}`}
          data-testid={category === name ? 'active-category-link' : 'category-link'}
          key={name}
          onClick={(e) => {
            handleClick(name);
          }}
          className={`uppercase px-4 py-2 rounded text-gray-700 
            ${category === name ? 'border-b-4 border-green-400' : 'bg-white hover:bg-green-100'}`}
        >
          {name}
        </Link>
        ))}
      </div>
      <div>
        <Link to="/">
          <img src={logo} alt="logo" className="w-1/2" />
        </Link>
      </div>
      <button
        onClick={onCartToggle}
        data-testid="cart-btn"
        className="relative p-2"
      >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth="1.5" stroke="currentColor" className="h-6 w-6 text-green-400">
          <path strokeLinecap="round" strokeLinejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
        </svg>
        {totalQuantity > 0 && (
          <span className="absolute -top-1 -right-1 bg-green-400 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
            {totalQuantity}
          </span>
        )}
      </button>
    </header>
  );
}
