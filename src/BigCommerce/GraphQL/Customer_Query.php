<?php

namespace BigCommerce\GraphQL;

/**
 * @class Product_Query
 *
 * Handle product query data and fragments
 */
class Customer_Query {

	public function get_public_wishlist_query() {
		return 'query wishlist($entityIds: [Int!]) {
			customer {
				wishlists(filters: { entityIds: $entityIds }) {
					pageInfo {
						endCursor
						hasNextPage
						hasPreviousPage
						startCursor
						__typename
					}
					edges {
						node {
							entityId
							isPublic
							items {
								pageInfo {
									__typename
									endCursor
									hasNextPage
									hasPreviousPage
									startCursor
								}
								edges {
									node {
										entityId
										product {
											addToCartUrl
											availabilityV2 {
												description
												status
												__typename
											}
											defaultImage {
												... ImageFragment
											}
											description
											entityId
											id
											inventory {
												aggregated {
													availableToSell
													warningLevel
													__typename
												}
												hasVariantInventory
												isInStock
												__typename
											}
											name
											path
											prices {
												... PricesFragment
											}
											productOptions {
												... ProductOptions
											}
											variants {
												edges {
													node {
														__typename
													}
												}
											}
											__typename
										}
										productEntityId
										variantEntityId
										__typename
									}
								}
							}
							name
							token
							__typename
						}
					}
				}
			}
		}
		fragment ImageFragment on Image {
			altText
			isDefault
			url (width: 320)
			urlOriginal
			__typename
		}
		fragment PricesFragment on Prices {
			basePrice {
				currencyCode
				value
				__typename
			}
			bulkPricing {
				maximumQuantity
				minimumQuantity
				__typename
			}
			mapPrice {
				currencyCode
				value
				__typename
			}
			price {
				currencyCode
				value
				__typename
			}
			priceRange {
				max {
					currencyCode
					value
					__typename
				}
				min {
					currencyCode
					value
					__typename
				}
				__typename
			}
			retailPrice {
				currencyCode
					value
					__typename
			}
			retailPriceRange {
				max {
					currencyCode
					value
					__typename
				}
				min {
					currencyCode
					value
					__typename
				}
				__typename
			}
			salePrice {
				currencyCode
				value
				__typename
			}
			saved {
				currencyCode
				value
				__typename
			}
			__typename
		}
		fragment ProductOptions on ProductOptionConnection {
			edges {
				node {
					entityId
					isRequired
					displayName
					isVariantOption
					__typename
					... CheckboxOptions
					... DateFieldOptions
					... FileUploadFieldOptions
					... MultiLineTextFieldOptions
					... MultipleChoiceOptions
					... NumberFieldOptions
					... TextFieldOptions
				}
			}
		}
		fragment CheckboxOptions on CheckboxOption {
			checkedByDefault
			label
			__typename
		}
		fragment DateFieldOptions on DateFieldOption {
			defaultDate: defaultValue
			earliest
			latest
			limitDateBy
			__typename
		}
		fragment FileUploadFieldOptions on FileUploadFieldOption {
			fileTypes
			maxFileSize
			__typename
		}
		fragment MultiLineTextFieldOptions on MultiLineTextFieldOption {
			defaultValue
			maxLength
			maxLines
			minLength
			__typename
		}
		fragment MultipleChoiceOptions on MultipleChoiceOption {
			displayStyle
			values(first: 10) {
				edges {
					node {
						entityId
						isDefault
						label
						__typename
						... on ProductPickListOptionValue {
							productId
						}
						... on SwatchOptionValue {
							hexColors
							imageUrl(width: 200)
						}
					}
				}
			}
			__typename
		}
		fragment NumberFieldOptions on NumberFieldOption {
			defaultNumber: defaultValue
			highest
			isIntegerOnly
			limitNumberBy
			lowest
			__typename
		}
		fragment TextFieldOptions on TextFieldOption {
			defaultValue
			maxLength
			minLength
			__typename
		}';
	}

	public function get_wishlist_query() {
		return 'query wishlist($entityIds: [Int!]) {
			customer {
				wishlists(filters: { entityIds: $entityIds }) {
					pageInfo {
						endCursor
						hasNextPage
						hasPreviousPage
						startCursor
						__typename
					}
					edges {
						node {
							entityId
							isPublic
							items {
								pageInfo {
									__typename
									endCursor
									hasNextPage
									hasPreviousPage
									startCursor
								}
								edges {
									node {
										entityId
										product {
											defaultImage {
												... ImageFragment
											}
											entityId
											id
											name
											path
											prices {
												... PricesFragment
											}
										}
										productEntityId
										variantEntityId
										__typename
									}
								}
							}
							name
							token
							__typename
						}
					}
				}
			}
		}
		fragment ImageFragment on Image {
			altText
			isDefault
			url (width: 320)
			urlOriginal
			__typename
		}
		fragment PricesFragment on Prices {
			basePrice {
				currencyCode
				value
				__typename
			}
			bulkPricing {
				maximumQuantity
				minimumQuantity
				__typename
			}
			mapPrice {
				currencyCode
				value
				__typename
			}
			price {
				currencyCode
				value
				__typename
			}
			priceRange {
				max {
					currencyCode
					value
					__typename
				}
				min {
					currencyCode
					value
					__typename
				}
				__typename
			}
			retailPrice {
				currencyCode
					value
					__typename
			}
			retailPriceRange {
				max {
					currencyCode
					value
					__typename
				}
				min {
					currencyCode
					value
					__typename
				}
				__typename
			}
			salePrice {
				currencyCode
				value
				__typename
			}
			saved {
				currencyCode
				value
				__typename
			}
			__typename
		}';
	}

	public function get_wishlists_query() {
		return 'query {
			customer {
				wishlists {
					pageInfo {
						hasNextPage
						hasPreviousPage
						__typename
						startCursor
						endCursor
					}
					edges {
						node {
							__typename
							entityId
							name
							isPublic
							token
							items {
								pageInfo {
									hasNextPage
									hasPreviousPage
									__typename
									startCursor
									endCursor
								}
								edges {
									node {
										entityId
										__typename
										productEntityId
										variantEntityId
									}
								}
							}
						}
					}
				}
			}
		}';
	}
}
