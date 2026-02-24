<?php

namespace BigCommerce\GraphQL;

/**
 * Handle product query data and fragments.
 * 
 * This class provides methods to fetch paginated product data, individual product details, and various product-related information
 * through fragments and query construction.
 */
class Product_Query {

	/**
	 * Get paginated products query.
	 *
	 * This method constructs a GraphQL query to retrieve a paginated list of products.
	 *
	 * @return string The GraphQL query string for paginated products.
	 */
	public function get_paginated_products_query() {
		return 'query paginateProducts(
			$pageSize: Int!
			$cursor: String!
		) {
			site {
				products (first: $pageSize, after:$cursor) {
					pageInfo {
						startCursor
						endCursor
					}
					edges {
						cursor
						node {
							entityId
							name
							id
							categories {
					            edges {
					                node {
					                    entityId
					                }
					            }
					        }
				            brand {
				                entityId
				            }
						}
					}
				}
			}
		}';
	}

	/**
	 * Get full paginated product request query with locale.
	 *
	 * This method constructs a GraphQL query to retrieve a paginated list of products, including locale information.
	 *
	 * @return string The full GraphQL query string for paginated products with locale.
	 */
	public function get_product_paginated_request_full() {
		return 'query paginateProducts(
			$pageSize: Int!
			$cursor: String!
			$locale: String=""
			$hasLocale: Boolean=false
		) {
			site {
				products (first: $pageSize, after:$cursor) {
					pageInfo {
						startCursor
						endCursor
					}
					edges {
						cursor
						node {
							' . $this->get_product_fragment() . '
						}
					}
				}
			}
		}' . $this->get_product_info_fragment();
	}

	/**
	 * Get product query for a specific product by path.
	 *
	 * This method constructs a GraphQL query to retrieve a single product based on its path.
	 *
	 * @return string The GraphQL query string for fetching a specific product by path.
	 */
	public function get_product_query() {
		return 'query getProduct(
			$hasLocale: Boolean = false
			$locale: String = "null"
			$path: String!
			) {
			site {
			  route(path: $path) {
			    node { ' . $this->get_product_fragment() . '
			    }
			  }
			}
		}' . $this->get_product_info_fragment();
	}

	/**
	 * Get the product fragment used in queries.
	 *
	 * This method provides a fragment that defines the structure of a product object in GraphQL queries.
	 *
	 * @return string The GraphQL fragment for product details.
	 */
	public function get_product_fragment() {
		return '__typename
	      ... on Product {
	        ...productInfo
	        variants(first: 250) {
	          edges {
	            node {
	              entityId
	              isPurchasable
	              sku
	              defaultImage {
	                urlOriginal
	                altText
	                isDefault
	              }
	              prices {
	                ...productPrices
	              }
	              inventory {
	                aggregated {
	                  availableToSell
	                  warningLevel
	                }
	                isInStock
	              }
	              productOptions {
	                edges {
	                  node {
	                    __typename
	                    entityId
	                    displayName
	                    ...multipleChoiceOption
	                  }
	                }
	              }
	            }
	          }
	        }
	      }';
	}

	/**
	 * Return product info query fragment.
	 *
	 * This method returns the GraphQL fragment that includes product information like name, SKU, and pricing.
	 *
	 * @return string The GraphQL fragment for product info.
	 */
	public function get_product_info_fragment(): string {
		return 'fragment productInfo on Product {
		    entityId
		    name
		    path
		    sku
		    addToCartUrl
		    condition
		    gtin
		     height {
		        unit
		        value
		        __typename
		     }
		     width {
		         unit
		        value
		        __typename
		     }
			depth {
		        unit
		        value
		        __typename
		    }
		    reviewSummary {
		        numberOfReviews
		        __typename
		        summationOfRatings
		    }
		    reviews {
		        __typename
		        edges {
		          node {
		            entityId
		            author {
		              name
		              __typename
		            }
		            title
		            text
		            rating
		            createdAt {
		              utc
		              __typename
		            }
		            __typename
		          }
		        }
		    }
		    giftWrappingOptions {
		        edges {
		          node {
		            entityId
		            __typename
		            name
		            allowComments
		            previewImageUrl
		          }
		        }
		        __typename
		      }
				    defaultImage {
		        altText
		        isDefault
		        url(width: 320)
		        urlOriginal
		        __typename
		    }
		    customFields {
		        edges {
		          node {
		            entityId
		            __typename
		            name
		            value
		          }
		          __typename
		        }
		    }
		    availabilityV2 {
	            description
	            status
	            __typename
			}
		    reviewSummary {
	            summationOfRatings
	            numberOfReviews
	        }
		    brand {
				entityId
				defaultImage {
					url(width: 320)
					altText
					urlOriginal
					isDefault
				}
				name
				id
				path
				__typename
				searchKeywords
				seo {
				  pageTitle
				  metaKeywords
				  metaDescription
				  __typename
				}
			}
			categories {
				edges {
					node {
						breadcrumbs(depth: 3) {
							edges {
								node {
									name
									entityId
									__typename
								}
							}
							__typename
						}
						entityId
						id
						name
						path
						__typename
						description
						defaultImage {
							url(width: 320)
							altText
							urlOriginal
							isDefault
						}
						seo {
							pageTitle
							metaKeywords
							metaDescription
							__typename
						}
					}
				}
			}
			maxPurchaseQuantity
			minPurchaseQuantity
			mpn
			plainTextDescription
		    inventory {
			    isInStock
			    hasVariantInventory
			    aggregated {
			        availableToSell
			    }
			}
		    description
		    prices {
		      ...productPrices
		    }
		    images {
		      edges {
		        node {
		          urlOriginal
		          altText
		          isDefault
		        }
		      }
		    }
		    reviewSummary {
		      numberOfReviews
		      summationOfRatings
		    }
		    variants(first: 250) {
		      edges {
		        node {
		          entityId
		          defaultImage {
		            urlOriginal
		            altText
		            isDefault
		          }
		        }
		      }
		    }
		    productOptions {
		      edges {
		        node {
		          __typename
		          entityId
		          displayName
		          ...multipleChoiceOption
		          ...checkboxOption
		        }
		      }
		    }
		    localeMeta: metafields(namespace: $locale, keys: ["name", "description"])
		      @include(if: $hasLocale) {
		      edges {
		        node {
		          key
		          value
		        }
		      }
		    }
        }
        ' . $this->get_product_prices_fragment() . '
        ' . $this->get_multiple_choice_options_fragment() . '
        ' . $this->get_checkbox_option_fragment();
	}

	/**
	 * Get product prices fragment.
	 *
	 * This method returns a fragment that defines the pricing structure for products in GraphQL queries.
	 *
	 * @return string The GraphQL fragment for product prices.
	 */
	public function get_product_prices_fragment(): string {
		return 'fragment productPrices on Prices {
		    price {
		      value
		      currencyCode
		    }
		    salePrice {
		      value
		      currencyCode
		    }
		    retailPrice {
		      value
		      currencyCode
		    }
		    basePrice {
		      value
		      currencyCode
		    }
		}';
	}

	/**
	 * Get multiple choice options fragment.
	 *
	 * This method returns a fragment defining multiple choice options available for the product in GraphQL queries.
	 *
	 * @return string The GraphQL fragment for multiple choice options.
	 */
	public function get_multiple_choice_options_fragment(): string {
		return 'fragment multipleChoiceOption on MultipleChoiceOption {
			values {
			  edges {
			    node {
			      entityId
			      label
			      isDefault
			      ...swatchOption
			      ...productPickListOption
			    }
			  }
			}
		}
		' . $this->get_swatch_options_fragment() . '
		' . $this->get_product_picklist_fragment();
	}

	/**
	 * Get swatch options fragment.
	 *
	 * This method returns a fragment that defines the swatch options available for the product.
	 *
	 * @return string The GraphQL fragment for swatch options.
	 */
	public function get_swatch_options_fragment(): string {
		return 'fragment swatchOption on SwatchOptionValue {
			isDefault
			hexColors
		}';
	}

	/**
	 * Get product picklist fragment.
	 *
	 * This method returns a fragment that defines the product picklist options available for the product.
	 *
	 * @return string The GraphQL fragment for product picklist options.
	 */
	public function get_product_picklist_fragment(): string {
		return 'fragment productPickListOption on ProductPickListOptionValue {
	        productId
		}';
	}

	/**
	 * Get checkbox option fragment.
	 *
	 * This method returns a fragment that defines the checkbox options available for the product.
	 *
	 * @return string The GraphQL fragment for checkbox options.
	 */
	public function get_checkbox_option_fragment(): string {
		return 'fragment checkboxOption on CheckboxOption {
			checkedByDefault
		}';
	}

}
