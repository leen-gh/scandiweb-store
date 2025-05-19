import React from 'react';
import { useQuery } from '@apollo/client';
import { GET_PRODUCTS_BY_CATEGORY } from '../graphQl/queries';
import ProductCard from './ProductCard';

const ProductList = ({ category, addTocart }) => {
  
  const { loading, error, data } = useQuery(GET_PRODUCTS_BY_CATEGORY, {
    variables: { category },
    skip: !category,
  });

  if (!category) return <p className="p-4">Select a category</p>;
  if (loading) return <p className="p-4">Loading products...</p>;
  if (error) {
    console.error('Product query error:', error);
    return <p className="p-4 text-red-500">Error loading products</p>;
  }

  const products = data?.categoryProducts || [];

  if (!products.length) {
    return <p className="p-4">No products found for this category.</p>;
  }

  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 p-4">
      {products.map((product) => (
        <ProductCard key={product.id} product={product} addTocart={addTocart} />
      ))}
    </div>
  );
};

export default ProductList;
