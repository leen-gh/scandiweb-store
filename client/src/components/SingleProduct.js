import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import parse from 'html-react-parser';
import { useQuery } from '@apollo/client';
import { GetProductById } from '../graphQl/queries';

export default function SingleProduct({ addTocart, openCart  }) {
  const { id } = useParams();
  const [selectedAttributes, setSelectedAttributes] = useState({});
  const [mainImage, setMainImage] = useState(null);

  const { loading, error, data } = useQuery(GetProductById, {
    variables: { id },
    skip: !id,
  });

  const product = data?.product;

  useEffect(() => {
    if (product?.gallery?.length) {
      setMainImage(product.gallery[0]);
    }
  }, [product]);

  const handleAttributeSelect = (attrName, option) => {
    setSelectedAttributes((prev) => ({ ...prev, [attrName]: option }));
  };

  const handleAddToCart = () => {

    const cartItem = {
      name: product.name,
      price: product.price,
      image: product.gallery[0],
      selectedAttributes,
      attributes: product.attributes.map(attr => ({
        name: attr.name,
        options: attr.options,
      })),
    };

    addTocart(cartItem);
    openCart(); 
    alert('Product added to cart!');
  };

  const allSelected = product?.attributes?.every(attr => selectedAttributes[attr.name]);
  const inStock = product?.in_stock;
  const dis_btn = (allSelected && inStock)





  if (loading) return <p className="p-4">Loading product...</p>;
  if (error || !product) return <p className="p-4 text-red-500">Product not found.</p>;


  return (
    <div className="grid grid-cols-1 md:grid-cols-7 gap-8 p-6">
      
      <div className="flex flex-col gap-2" data-testid="product-gallery">
        {product.gallery.map((img, idx) => (
          <img
            key={idx}
            src={img}
            alt={`gallery-${idx}`}
            onClick={() => setMainImage(img)}
            className={`w-20 h-20 object-cover cursor-pointer border ${mainImage === img ? 'border-black' : ''}`}
          />
        ))}
      </div>

      <div className="col-span-3">
        <img src={mainImage} alt="main" className="w-full max-h-[500px] object-contain" />
      </div>

      <div className="col-span-2">
        <h1 className="text-2xl font-bold my-4">{product.name}</h1>

        {product.attributes?.map((attr) => (
          <div key={attr.name} className="mb-4" data-testid={`product-attribute-${attr.name.toLowerCase().replace(/\s+/g, '-')}`}>
            <h2 className="font-semibold text-lg mb-2">{attr.name}</h2>
            <div className="flex gap-2">
              {attr.options.map((option) => {
                const isSelected = selectedAttributes[attr.name] === option;
                const isColor = attr.name.toLowerCase() === 'color';
                return (
                  <button
                    key={option}
                    onClick={() => handleAttributeSelect(attr.name, option)}
                    className={`border rounded cursor-pointer transition 
                      ${isSelected ? 'bg-black text-white border-2 border-green-400' : 'bg-gray-100'}
                      ${isColor ? 'px-4 py-4' : 'px-2 py-1'}`}
                    style={isColor ? { backgroundColor: option } : {}}
                    data-testid={`product-attribute-${attr.name.toLowerCase().replace(/\s+/g, '-')}-${option}`}
                  >
                    {!isColor && option}
                  </button>
                );
              })}
            </div>
          </div>
        ))}

        <div className="my-4">
          <h3 className="text-lg font-semibold">Price:</h3>
          <p className="text-xl font-bold">${product.price.toFixed(2)}</p>
        </div>

        <button
          onClick={handleAddToCart}
          data-testid="add-to-cart"
          disabled={!dis_btn}
          className="w-full px-6 py-3 bg-green-400 text-white rounded hover:bg-green-500 disabled:bg-gray-400 transition"
        >
          Add to Cart
        </button>

        <div className="mt-6 prose" data-testid="product-description">
          {product.description ? parse(product.description) : 'No description.'}
        </div>
      </div>
    </div>
  );
}
