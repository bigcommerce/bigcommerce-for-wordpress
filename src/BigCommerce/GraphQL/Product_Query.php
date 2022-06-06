<?php

namespace BigCommerce\GraphQL;

/**
 * @class Product_Query
 *
 * Handle product query data and fragments
 */
class Product_Query {

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
	 * Return product info query fragment
	 *
	 * @return string
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

	public function get_swatch_options_fragment(): string {
		return 'fragment swatchOption on SwatchOptionValue {
			isDefault
			hexColors
		}';
	}

	public function get_product_picklist_fragment(): string {
		return 'fragment productPickListOption on ProductPickListOptionValue {
	        productId
	    }';
	}

	public function get_checkbox_option_fragment(): string {
		return 'fragment checkboxOption on CheckboxOption {
		    checkedByDefault
		}';
	}

}
