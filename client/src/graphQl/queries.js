import { gql } from '@apollo/client';

export const GET_CATEGORIES = gql`
  query GetCategories {
    categories {
      name
    }
  }
`;

export const GET_PRODUCTS_BY_CATEGORY = gql`
  query GetProductsByCategory($category: String!) {
    categoryProducts(category: $category) {
      id
      name
      brand
      in_stock
      price
      gallery
      
    }
  }
`;

export const GetProductById = gql`
    query GetProduct($id: ID!) {
    product(id: $id) {
    id
    name
    brand
    description
    in_stock
    category
    gallery
    price
    attributes {
      name
      options
    }
  }
}
`;




