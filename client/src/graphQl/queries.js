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

export const PLACE_ORDER = gql`
  mutation PlaceOrder($items: [OrderItemInput!]!, $totalPrice: Float!) {
    placeOrder(items: $items, totalPrice: $totalPrice) {
      id
      total_price
      created_at
    }
  }
`;




