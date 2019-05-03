# Changelog

## 1.52.0

### New features

- Updated `\Wizaplace\SDK\Discussion\DiscussionService::submitContactRequest` add 3 optional params `$recipientEmail`, `$attachmentsUrls` and `$files`

## 1.51.0

### New features

- Added `\Wizaplace\SDK\Order\OrderItem::getSupplierRef`
- Added `\Wizaplace\SDK\Pim\Product\ProductService::updateStock`
- Added `Wizaplace\SDK\Catalog\Category::getSeoKeywords`
- Added `Wizaplace\Vendor\Order\AmountTaxesDetail` Represent an amount by its 3 values: excluding taxes, taxes and including taxes
- Added `Wizaplace\Vendor\Order\AmountsTaxesDetails` Collection of AmountTaxesDetail
- Added `Wizaplace\Vendor\Order\Order::getAmountsTaxesDetails()`
- Added `Wizaplace\Vendor\Order\Order::getTotalsTaxesDetail()`
- Added `Wizaplace\Vendor\Order\Order::getShippingCostsTaxesDetail()`
- Added `Wizaplace\Vendor\Order\Order::getCommissionsTaxesDetail()`
- Added `Wizaplace\Vendor\Order\Order::getVendorShareTaxesDetail()`

## 1.50.0

### New features

- Added `\Wizaplace\SDK\Catalog\Product::getAvailableSince`
- Added `\Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProductFilter`
- Added `\Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProductList`
- Added `\Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProductService::getListMultiVendorProduct`
- Added `\Wizaplace\SDK\Pim\Product\ProductListFilter:byIds`
- Added `\Wizaplace\SDK\Pim\Product\ProductListFilter:bySupplierReferences`
- Added `\Wizaplace\SDK\Pim\Product\ProductListFilter:byProductCodes`

## 1.49.0

## 1.48.1

### New features

- Added `\Wizaplace\SDK\Catalog\CatalogService::getProductsByFilters`
- Added `\Wizaplace\SDK\Catalog\ProductFilter`

## 1.48.0

- Added `\Wizaplace\SDK\Order\Order::getShippingCost`
- Added `\Wizaplace\SDK\Order\Order::getDiscount`

### New features

- Added `\Wizaplace\SDK\Order\OrderService::getPayment`
- Added `\Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProductService::addHostedVideoToMultiVendorProduct`
- Added `\Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProductService::addUploadedVideoToMultiVendorProduct`
- Added `\Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProductService::deleteVideoToMultiVendorProduct`
- Added `\Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProduct::getVideo`
- Added `\Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProductVideo`

## 1.47.0

## 1.46.1

- Deprecated `\Wizaplace\SDK\Company\CompanyService::registerC2CCompany` use `CompanyService::register` instead
- Added `\Wizaplace\SDK\Company\CompanyC2CRegistration`

### Bugfixes

- Fixed `Wizaplace\SDK\Vendor\Promotion\BasketPromotion` missing target property

## 1.46.0

### New features

- Added `\Wizaplace\SDK\User\User::getPhone`
- Added `\Wizaplace\SDK\Pim\Product\ProductService::addVideo`
- Added `\Wizaplace\SDK\Pim\Product\ProductService::deleteVideo`
- Added `Wizaplace\SDK\Division\Division::getMaxLevel`
- Added `Wizaplace\SDK\Company\CompanyService::updateCompanyImage`
- Added `Wizaplace\SDK\Company\CompanyService::deleteCompanyImage`
- Added parameter `files` to `Wizaplace\SDK\Company\CompanyService::registerC2Ccompany`
- Added `Wizaplace\SDK\Catalog\ProductOffer::getStatus`

## 1.45.0

## 1.44.14

### Bugfixes

- Fixed `\Wizaplace\SDK\Vendor\Order\OrderStatus` with new status

## 1.44.13

 ### New features

- Added `\Wizaplace\SDK\Company\CompanyRegistration::getIban`
- Added `\Wizaplace\SDK\Company\CompanyRegistration::setIban`
- Added `\Wizaplace\SDK\Company\CompanyRegistration::getBic`
- Added `\Wizaplace\SDK\Company\CompanyRegistration::setBic`
- Added `\Wizaplace\SDK\Company\Company::getIban`
- Added `\Wizaplace\SDK\Company\Company::getBic`
- Updated `\Wizaplace\SDK\Company\CompanyService::registerC2CCompany` add 2 optional params `$iban` and `$bic`

## 1.44.12

### New features

- Added `\Wizaplace\SDK\User\UpdateUserAddressCommand::getDivisionCode`
- Added `\Wizaplace\SDK\User\UpdateUserAddressCommand::setDivisionCode`

## 1.44.11

### Bugfixes

- Fixed `\Wizaplace\SDK\Catalog\ProductOffer` with null `$data['divisions']`
- Fixed `\Wizaplace\SDK\Catalog\ProductSummary::getOffers` return null 

## 1.44.10

### New features

- Updated `Wizaplace\SDK\Catalog\DeclinationOption`
- Updated `Wizaplace\SDK\Catalog\Option`

## 1.44.9

### New features

- Added `\Wizaplace\SDK\Basket\BasketItem::getDivisions`

## 1.44.8

### New features

- Added `\Wizaplace\SDK\Shipping\ShippingService::getAll`
- Added `\Wizaplace\SDK\Shipping\ShippingService::getById`
- Added `\Wizaplace\SDK\Shipping\ShippingService::put`
- Added `\Wizaplace\SDK\Shipping\ShippingRate`
- Added `\Wizaplace\SDK\Shipping\ShippingStatus`

## 1.44.7

### New features

- Added `\Wizaplace\SDK\Catalog\ProductSummary::getOffers`

## 1.44.6

### New features

- Added `\Wizaplace\SDK\Basket\BasketItem::getProductCode`
- Updated `\Wizaplace\SDK\Vendor\Order\OrderService::acceptOrder` you can set a invoice number
- Added `Wizaplace\SDK\Vendor\Order\OrderService::getHandDeliveryCodes`
- Added `\Wizaplace\SDK\Pim\Product\ProductSummary::getDivisions`

## 1.44.5

### Breaking Changes

- Rename `\Wizaplace\SDK\Division\Division::getDescription` into `\Wizaplace\SDK\Division\Division::getName`

### Corrections

- Fixed an issue with `\Wizaplace\SDK\Division\Division::isEnabled`
- Fixed an issue with `\Wizaplace\SDK\Division\Division::getDisabledBy`

## 1.44.4

### New features

- `\Wizaplace\SDK\Vendor\Order\OrderService::reportHandDelivery` can now throw SomeParametersAreInvalid and AccessDenied

## 1.44.3

### New features

- Updated `\Wizaplace\SDK\Catalog\Product` with `\Wizaplace\SDK\Division\Division`
- Added `\Wizaplace\SDK\Division\DivisionService::get`
- Added `\Wizaplace\SDK\Company\CompanyService::getDivisionsCountriesCodes`
- Added `\Wizaplace\SDK\Division\DivisionCompany`
- Added `\Wizaplace\SDK\Company\CompanyService::getDivisions`
- Added `\Wizaplace\SDK\Company\CompanyService::setDivisions`
- Added `\Wizaplace\SDK\Pim\Product\ProductService::getDivisionsCountriesCodes`
- Added `\Wizaplace\SDK\Pim\Product\ProductService::getDivisions`
- Added `\Wizaplace\SDK\Pim\Product\ProductService::setDivisions`
- Added `\Wizaplace\SDK\Division\DivisionService::set`
- Added `\Wizaplace\SDK\User\UserAddress::getDivision`
- Added `\Wizaplace\SDK\Catalog\ProductOffer`
- Added `\Wizaplace\SDK\Catalog\Product::getOffers`

## 1.44.2

### New features

- Added `\Wizaplace\SDK\Basket\BasketItem::getProductCode`

## 1.44.1

### New features

- Added `Wizaplace\SDK\Vendor\Order\OrderService::getHandDeliveryCodes`

## 1.44.0

### New features

- Added `Wizaplace\SDK\Vendor\Order\Payment::getCommitmentDate`

### Corrections

- Fixed an issue with `Wizaplace\SDK\User\UserService::enable` and `Wizaplace\SDK\User\UserService::disable`

## 1.43.0

### New features

- Added optional parameter `$declineReason` to `\Wizaplace\SDK\Vendor\Order\OrderService::declineOrder`
- Added `Wizaplace\SDK\Vendor\Order\Order::getDeclineReason`
- Added `Wizaplace\SDK\Basket\BasketService::setMondialRelayPickupPoint`
- Added `Wizaplace\SDK\Shipping\MondialRelayService` and its associated classes
- Added `Wizaplace\SDK\Vendor\Order\OrderService::generateMondialRelayLabel`
- Added `\Wizaplace\SDK\Cms\MenuItem::isTargetBlank`
- Added `\Wizaplace\SDK\Pim\Option::getStatus`
- Added `\Wizaplace\SDK\Pim\Option::isEnabled`
- Added `\Wizaplace\SDK\Pim\Option::isDisabled`
- Added `\Wizaplace\SDK\Pim\OptionStatus`
- Added `Wizaplace\SDK\Catalog\CatalogService::getDeclinationById`
- Updated `Wizaplace\SDK\Catalog\Declination`

## 1.42.0

### New features

- Added `\Wizaplace\SDK\Order\OrganisationOrderService::getOrder`
- Added attribute `hidden` to `\Wizaplace\SDK\Organisation\OrganisationBasket`

## 1.41.0

Compatible with Wizaplace 1.27.0

### New requirements

- "psr/log": "^1.0"

### New features

- Added optional parameter `$requestLogger` to the constructor of `\Wizaplace\SDK\ApiClient`
- Added `\Wizaplace\SDK\Order\OrderItem::getProductImageId`
- Added `\Wizaplace\SDK\User\User::getType`
- Added enum `\Wizaplace\SDK\User\UserType`
- Added pagination data to the array returned by `\Wizaplace\SDK\Organisation\OrganisationService::getOrganisationOrders`
- Added `\Wizaplace\SDK\Organisation\OrganisationBasket::isCheckout`

## 1.40.0

### New features

- Added optional parameter `$language` to `\Wizaplace\SDK\Catalog\CatalogService::getAllProducts`
- Added `\Wizaplace\SDK\Catalog\Shipping::getDeliveryTime`
- Added `\Wizaplace\SDK\Basket\Shipping::getImage`
- Added `\Wizaplace\SDK\Organisation\OrganisationService::checkoutBasket`
- Updated `\Wizaplace\SDK\Organisation\OrganisationService::getOrganisationOrders`
- Updated `\Wizaplace\SDK\Vendor\OrderSymmary`
- Added `\Wizaplace\SDK\Catalog\Product::hasInfiniteStock`
- Added `\Wizaplace\SDK\Catalog\Declination::hasInfiniteStock`
- Added `\Wizaplace\SDK\Catalog\DeclinationSummary::hasInfiniteStock`
- Added `\Wizaplace\SDK\PIM\Product\Product::hasInfiniteStock`
- Added `\Wizaplace\SDK\PIM\Product\ProductDeclinationUpsertData::setInfiniteStock`
- Added `\Wizaplace\SDK\PIM\Product\ProductUpsertData::setInfiniteStock`
- Added `\Wizaplace\SDK\Vendor\Order\Order::getCompanyId`
- Added `\Wizaplace\SDK\Vendor\Order\OrderSummary::getCompanyId`

## 1.39.0

### New features

- Added `\Wizaplace\SDK\Pim\Product::getShippings`

## 1.38.0

### New features

- Added `\Wizaplace\SDK\Organisation\OrganisationBasket::getUserId`
- Added `\Wizaplace\SDK\Catalog\Product::getSeoKeywords`
- Added `\Wizaplace\SDK\PIM\Product::getShipping`
- Added `\Wizaplace\SDK\PIM\Product::putShipping`
- Added `\Wizaplace\SDK\PIM\UpdateShippingCommand`

### Corrections

- Fixed an issue for MVP with `Wizaplace\SDK\Catalog\Product::getSeoDescription`

### 1.37.0

### New features

- Added optional parameters `$billing` and `$shipping` to `\Wizaplace\SDK\User\UserService::register`
- Added `\Wizaplace\SDK\Order\Order::getCompanyName`

### Corrections

- Fixed an issue in `\Wizaplace\SDK\User\UserService::updateUserAddresses` where address fields would not be updated

## 1.36.0

### New features

- Added `\Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProductService::addImageToMultivendorProduct`
- Added `\Wizaplace\SDK\Organisation\OrganisationService::getGroupUsers`

### Corrections

- Added `\Wizaplace\SDK\Pim\Product::STANDBY`

## 1.35.0

### New features

- Added `\Wizapalce\SDK\Catalog\Review\ReviewService::canUserReviewProduct`
- Added `\Wizaplace\SDK\Organisation\OrganisationService::createGroup`

## 1.34.0

### New features

- Added `\Wizaplace\SDK\Company\CompanyService::updateFile`
- Added `\Wizaplace\SDK\Company\CompanyService::deleteFile`
- Added `\Wizaplace\SDK\Organisation\OrganisationService::getOrganisationFromUserId`
- Added `\Wizaplace\SDK\Organisation\OrganisationService::addUserToGroup`
- Added `\Wizaplace\SDK\Organisation\OrganisationService::removeUserToGroup`
- Added `\Wizaplace\SDK\Organisation\OrganisationService::getOrganisationBaskets`
- Added `\Wizaplace\SDK\Organisation\OrganisationService::addNewUser`

## 1.33.0

### New features

- Added `\Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProductService::getMultiVendorProductById`
- Added `\Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProductService::createMultiVendorProduct`
- Added `\Wizaplace\SDK\Pim\MultiVendorProduct\MultiVendorProductService::updateMultiVendorProduct`

## 1.32.0

### New features

- Added `\Wizaplace\SDK\Catalog\DeclinationSummary::getShortDescription`

## 1.31.0

### New features

- Added `\Wizaplace\SDK\Basket\BasketItem::getGreenTax`
- Added `\Wizaplace\SDK\Order\OrderItem::getGreenTax`
- Added `\Wizaplace\SDK\Organisation\OrganisationService::addBasket`
- Added `\Wizaplace\SDK\Organisation\OrganisationService::basketValidation`
- Added `\Wizaplace\SDK\Organisation\OrganisationService::get`
- Added `\Wizaplace\SDK\Organisation\OrganisationService::getList`
- Added `\Wizaplace\SDK\Organisation\OrganisationService::getListUsers`
- Added `\Wizaplace\SDK\Organisation\OrganisationService::lockBasket`
- Added `\Wizaplace\SDK\Organisation\OrganisationService::organisationAddressesUpdate`
- Added `\Wizaplace\SDK\Organisation\OrganisationService::organisationUpdate`
- Added `\Wizaplace\SDK\Organisation\OrganisationService::register`
- Added `\Wizaplace\SDK\Organisation\Organisation`
- Added `\Wizaplace\SDK\Organisation\OrganisationAddress`
- Added `\Wizaplace\SDK\Organisation\OrganisationAdministrator`
- Introduce a new detailed exception `\Wizaplace\SDK\Exception\UserDoesntBelongToOrganisation`

## 1.30.0

### New features

- Added `\Wizaplace\SDK\Company\Company::getCompanyFiles`
- Added `\Wizaplace\SDK\Company\Company::fetchFile`

## 1.29.0

### New features

- Added `\Wizaplace\SDK\Catalog\CatalogService::getAllProducts`

## 1.28.0

### New features

- Added `\Wizaplace\SDK\Catalog\CompanyDetail::getExtra`
- Added `\Wizaplace\SDK\Company\Company::getExtra`
- Added `\Wizaplace\SDK\Company\CompanyRegistration::getExtra`
- Added `\Wizaplace\SDK\Company\CompanyRegistration::setExtra`
- Added `\Wizaplace\SDK\Company\CompanyUpdateCommand::getExtra`
- Added `\Wizaplace\SDK\Company\CompanyUpdateCommand::setExtra`
- Added `\Wizaplace\SDK\Cms\Banner::getName`
- Added `\Wizaplace\SDK\Company\CompanyService::getCompany`

## 1.27.0

### New features

- Added `\Wizaplace\SDK\Pim\Product\ProductService::getProductById` and associated classes
- Added `\Wizaplace\SDK\Pim\Product\ProductService::listProducts` and associated classes
- Added `\Wizaplace\SDK\Pim\Product\ProductService::createProduct` and associated classes
- Added `\Wizaplace\SDK\Pim\Product\ProductService::deleteProduct`
- Added `\Wizaplace\SDK\Vendor\Order\OrderService::acceptOrder`
- Added `\Wizaplace\SDK\Vendor\Order\OrderService::declineOrder`
- Added `\Wizaplace\SDK\Pim\Product\ProductService::updateProduct` and associated classes
- Added `\Wizaplace\SDK\Vendor\Order\OrderService::getOrderById` and associated classes
- Added `\Wizaplace\SDK\Vendor\Order\OrderService::listOrders` and associated classes
- Added `\Wizaplace\SDK\Pim\Attribute\AttributeService::getProductAttributes` and associated classes
- Added `\Wizaplace\SDK\Pim\Attribute\AttributeService::getProductAttribute` and associated classes
- Added `\Wizaplace\SDK\Pim\Attribute\AttributeService::setProductAttributeValue`
- Added `\Wizaplace\SDK\Pim\Attribute\AttributeService::setProductAttributeVariants`
- Added `\Wizaplace\SDK\Pim\Attribute\AttributeService::getCategoryAttributes` and associated classes
- Added `\Wizaplace\SDK\Pim\Option\OptionService::getCategoryOptions` and associated classes
- Added `\Wizaplace\SDK\Vendor\Order\OrderService::listShipments`
- Added `\Wizaplace\SDK\Vendor\Order\OrderService::getShipmentById`
- Added `\Wizaplace\SDK\Vendor\Order\OrderService::createShipment`
- Added `\Wizaplace\SDK\Vendor\Order\OrderService::setInvoiceNumber`
- Added `\Wizaplace\SDK\Vendor\Order\OrderService::reportHandDelivery`
- Added `\Wizaplace\SDK\User\User::getCompanyId`
- Added `\Wizaplace\SDK\User\User::isVendor`
- Added `\Wizaplace\SDK\Vendor\Order\OrderService::listTaxes` and associated classes
- Added `\Wizaplace\SDK\Pim\Category\CategoryService::listCategories`
- Added `\Wizaplace\SDK\Pim\Tax\TaxService::listTaxes`
- Added `\Wizaplace\SDK\Vendor\Promotion\CatalogPromotionService::getPromotion`
- Added `\Wizaplace\SDK\Vendor\Promotion\CatalogPromotionService::listPromotions`
- Added `\Wizaplace\SDK\Vendor\Promotion\CatalogPromotionService::savePromotion`
- Added `\Wizaplace\SDK\Vendor\Promotion\CatalogPromotionService::deletePromotion`
- Added `\Wizaplace\SDK\Vendor\Promotion\BasketPromotionService::getPromotion`
- Added `\Wizaplace\SDK\Vendor\Promotion\BasketPromotionService::listPromotions`
- Added `\Wizaplace\SDK\Vendor\Promotion\BasketPromotionService::savePromotion`
- Added `\Wizaplace\SDK\Vendor\Promotion\BasketPromotionService::deletePromotion`

## 1.26.0

### New features

- Added `\Wizaplace\SDK\Catalog\Category::getCategoryPath`
- Added `\Wizaplace\SDK\Basket\PaymentType::SEPA_DIRECT`
- Added `\Wizaplace\SDK\User\UserService::registerWithFullInfos`

## 1.25.0

### New features

- Added `\Wizaplace\SDK\Order\Order::getPayment`

## 1.24.0

### New features

- Added `\Wizaplace\SDK\ApiClient::getOAuthAuthorizationUrl`
- Added `\Wizaplace\SDK\ApiClient::setApplicationToken`

## 1.23.0

### New features

- Added `\Wizaplace\SDK\ApiClient::oauthAuthenticate`
- Added `\Wizaplace\SDK\Cms\MenuItem::getChildren`

## 1.22.0

### New features

- Added `\Wizaplace\SDK\Order\OrderService::commitOrder`

## 1.21.0

- Added `\Wizaplace\SDK\Catalog\CatalogService::getProductsByCode`
- Added `\Wizaplace\SDK\Catalog\CatalogService::getProductsBySupplierReference`

## 1.20.0

### New features

- Introduce a new detailed exception `\Wizaplace\SDK\Exception\OrderNotFound`
- Added `\Wizaplace\SDK\Basket\Payment::getType()`

## 1.19.0

### New features

- Introduce a new detailed exception `\Wizaplace\SDK\Exception\DeclinationIsNotActive`
- Introduce a new detailed exception `\Wizaplace\SDK\Exception\DiscussionNotFound`
- Introduce a new detailed exception `\Wizaplace\SDK\Exception\ProductAttachmentNotFound`
- Added `\Wizaplace\SDK\Company\CompanyService::update`
- Added `\Wizaplace\SDK\Catalog\CatalogService::getCategories`

## 1.18.0

### New features

- Added `\Wizaplace\SDK\Catalog\CompanyDetail::getFullAddress`
- Added `\Wizaplace\SDK\Catalog\CompanyListItem::getFullAddress`

## 1.17.0

### New features

- Added an optional `$geoFilter` parameter to `\Wizaplace\SDK\Catalog\CatalogService::search`
- Added `\Wizaplace\SDK\Order\Order::getBillingAddress`

## 1.16.0

### New features

- Introduce a new detailed exception `\Wizaplace\SDK\Exception\ProductNotFound`
- Introduce a new detailed exception `\Wizaplace\SDK\Exception\ReviewsAreDisabled`
- Introduce a new detailed exception `\Wizaplace\SDK\Exception\SenderIsAlsoRecipient`
- Introduce a new detailed exception `\Wizaplace\SDK\Exception\CompanyHasNoAdministrator`
- Introduce a new detailed exception `\Wizaplace\SDK\Exception\CompanyNotFound`
- Improve detection of `\Wizaplace\SDK\Favorite\Exception\FavoriteAlreadyExist`
- Introduce a new detailed exception `\Wizaplace\SDK\Exception\BasketIsEmpty`
- Added `\Wizaplace\SDK\Catalog\CatalogServiceInterface` which is implemented by `\Wizaplace\SDK\Catalog\CatalogService`
- Added `\Wizaplace\SDK\Catalog\AbstractCatalogServiceDecorator` which allows to easily decorate any `\Wizaplace\SDK\Catalog\CatalogServiceInterface`

## 1.15.1

- Fixed type errors on `\Wizaplace\SDK\Catalog\Facet\ListFacetValue::getCount` and `\Wizaplace\SDK\Catalog\Facet\ListFacetValue::getPosition`

## 1.15.0

### New features

- Added `\Wizaplace\SDK\Company\CompanyService::registerC2CCompany`
- Added `\Wizaplace\SDK\Basket\Basket::getShippingAddress`

## 1.14.0

### New features

- Added `\Wizaplace\SDK\Basket\Basket::isEligibleToPickupPointsShipping`
- Added `\Wizaplace\SDK\Basket\Basket::isPickupPointsShipping`
- Added `\Wizaplace\SDK\Basket\BasketService::setPickupPoint`

## 1.13.0

### New features

- `\Wizaplace\SDK\Basket\BasketService::addCoupon` now returns more detailed exceptions
- Added `\Wizaplace\SDK\Basket\Basket::getItemsPrice`
- Added `\Wizaplace\SDK\Basket\Basket::getShippingPrice`
- Added `\Wizaplace\SDK\Basket\Basket::getTotalPrice`
- Marked `\Wizaplace\SDK\Basket\Basket::getTotalShipping` as deprecated
- Marked `\Wizaplace\SDK\Basket\Basket::getTotalTax` as deprecated
- Marked `\Wizaplace\SDK\Basket\Basket::getTotal` as deprecated
- Added `\Wizaplace\SDK\Basket\BasketShippingGroup::getItemsPrice`
- Added `\Wizaplace\SDK\Basket\BasketShippingGroup::getShippingPrice`
- Added `\Wizaplace\SDK\Basket\BasketShippingGroup::getTotalPrice`
- Added `\Wizaplace\SDK\Basket\BasketItem::getUnitPrice`
- Added `\Wizaplace\SDK\Basket\BasketItem::getTotalPrice`
- Marked `\Wizaplace\SDK\Basket\BasketItem::getIndividualPrice` as deprecated
- Marked `\Wizaplace\SDK\Basket\BasketItem::getTotal` as deprecated

## 1.12.0

### New features

- `\Wizaplace\SDK\Catalog\DeclinationId` now implements `\JsonSerializable`
- Added `\Wizaplace\SDK\ApiClient::setLanguage`

## 1.11.0

### New features

- Added `\Wizaplace\SDK\Basket\BasketService::createEmptyBasket`

## 1.10.1

### Bugfixes

- Fix `\Wizaplace\SDK\Basket\BasketService::getUserBasketId` which sometimes returned an empty string instead of `null`

## 1.10.0

### New features

- Added `\Wizaplace\SDK\User\User::getCompanyId`
- Added `\Wizaplace\SDK\User\User::isVendor`

## 1.9.0

### New features

- Added `\Wizaplace\SDK\Catalog\CatalogService::getCompanies`

## 1.8.0

### New features

- Added `\Wizaplace\SDK\Catalog\DeclinationOption::getImage`
- Added `\Wizaplace\SDK\Catalog\OptionVariant::getImage`

## 1.7.0

### New features

- Added `\Wizaplace\SDK\Seo\SeoService::listSlugs` and associated classes

## 1.6.0

### New features

- Added `\Wizaplace\SDK\Catalog\AttributeVariant::getSeoTitle`
- Added `\Wizaplace\SDK\Catalog\AttributeVariant::getSeoDescription`
- Added `\Wizaplace\SDK\Catalog\Category::getSeoTitle`
- Added `\Wizaplace\SDK\Catalog\Category::getSeoDescription`

## 1.5.2

### Bugfixes

- Marked `\Wizaplace\SDK\Catalog\ProductAttributeValue::getId` as nullable
- Marked `\Wizaplace\SDK\Catalog\ProductAttributeValue::getAttributeId` as nullable

## 1.5.1

### Bugfixes

- Added a default value for `ListFacetValue`'s position, as it is not always sent by the API.

## 1.5.0

### New features

- Added `\Wizaplace\SDK\Company\UnauthenticatedCompanyRegistration::getLegalRepresentativeFirstName`
- Added `\Wizaplace\SDK\Company\UnauthenticatedCompanyRegistration::setLegalRepresentativeFirstName`
- Added `\Wizaplace\SDK\Company\UnauthenticatedCompanyRegistration::getLegalRepresentativeLastName`
- Added `\Wizaplace\SDK\Company\UnauthenticatedCompanyRegistration::setLegalRepresentativeLastName`
- Added `\Wizaplace\SDK\Company\CompanyService::unauthenticatedRegister`

## 1.4.0

### New features

- Added `\Wizaplace\SDK\Catalog\Product::getImages`

## 1.3.0

### New features

- Added `\Wizaplace\SDK\Catalog\Product::getSeoTitle`
- Added `\Wizaplace\SDK\Catalog\Product::getSeoDescription`
- Added `\Wizaplace\SDK\Catalog\Product::getCreatedAt`
- Added `\Wizaplace\SDK\Catalog\Product::getUpdatedAt`

## 1.2.0

### New features

- Added `\Wizaplace\SDK\Catalog\ProductAttribute::getValues`
- Deprecated `\Wizaplace\SDK\Catalog\ProductAttribute::getValueIds`
- Deprecated `\Wizaplace\SDK\Catalog\ProductAttribute::getValue`
- Added `\Wizaplace\SDK\Discussion\DiscussionService::startDiscussionWithVendor`
- Added `\Wizaplace\SDK\Discussion\DiscussionService::startDiscussionFromDeclinationId`

## 1.1.0

### New features

- Added `\Wizaplace\SDK\Order\OrderService::downloadPdfInvoice`
- Added `\Wizaplace\SDK\Discussion\DiscussionService::submitContactRequest`
- `\Wizaplace\SDK\Catalog\ProductSummary::getMainDeclinationId` now uses the ID given by the API instead of trying to guess it
- `\Wizaplace\SDK\Catalog\ProductAttribute::getImageUrls` is now marked as deprecated

## 1.0.1

### Bugfixes

- Fix `\Wizaplace\SDK\Basket\PaymentInformation::getRedirectUrl` returning an empty URI

## 1.0.0

### BREAKING CHANGES

- `\Wizaplace\SDK\Catalog\SearchProductAttribute::getValues` now returns an array of `\Wizaplace\SDK\Catalog\ProductAttributeValue` instead of an array of associative arrays
- `\Wizaplace\SDK\User\UserAddress::getTitle` now returns a nullable `\Wizaplace\SDK\User\UserTitle` instead of a string
- `\Wizaplace\SDK\Order\OrderReturn::getStatus` now returns an `\Wizaplace\SDK\Order\OrderReturnStatus` instead of a string
- `\Wizaplace\SDK\Basket\PaymentInformation::getRedirectUrl` now returns a nullable `\Psr\Http\Message\UriInterface` instead of a `string`
- `\Wizaplace\SDK\Company\Company::getUrl` now returns a nullable `\Psr\Http\Message\UriInterface` instead of a `string`
- `\Wizaplace\SDK\Catalog\ProductVideo::getThumbnailUrl` now returns an `\Psr\Http\Message\UriInterface` instead of a `string`
- `\Wizaplace\SDK\Catalog\ProductVideo::getVideoUrl` now returns an `\Psr\Http\Message\UriInterface` instead of a `string`
- `\Wizaplace\SDK\Image\ImageService::getImageLink` now returns an `\Psr\Http\Message\UriInterface` instead of a `string`
- Removed `\Wizaplace\SDK\Catalog\Product::getCreationDate`
- Removed `\Wizaplace\SDK\Catalog\ProductSummary::getCategorySlugs`
- Removed `\Wizaplace\SDK\Catalog\Product::getCategorySlugs`
- `\Wizaplace\SDK\Catalog\ProductSummary::getConditions` now returns an array of the enum `\Wizaplace\SDK\Catalog\Condition` instead of undocumented strings
- `\Wizaplace\SDK\Basket\PaymentInformation::getOrders` now returns an array of `\Wizaplace\SDK\Basket\BasketOrder` instead of an array of undocumented associative arrays
- `\Wizaplace\SDK\Catalog\ProductAttachment` class is now final
- `\Wizaplace\SDK\Catalog\ProductAttributeValue` class is now final
- `\Wizaplace\SDK\Catalog\Facet` has moved to `\Wizaplace\SDK\Catalog\Facet\Facet` and is now an abstract class with two subclasses: `\Wizaplace\SDK\Catalog\Facet\ListFacet` and `\Wizaplace\SDK\Catalog\Facet\NumericFacet`. A few methods have changed in the process
- `\Wizaplace\SDK\Catalog\DeclinationSummary::getProductId` now returns a `string` instead of an `int`
- All declinations' IDs were changed from `string` to `\Wizaplace\SDK\Catalog\DeclinationId`
- Added `\Wizaplace\SDK\Catalog\ProductSummary::getMainDeclinationId`

## 0.11.4

### New features

- Added `\Wizaplace\SDK\Order\Order::getCustomerComment`

## 0.11.3

### New features

- Added `\Wizaplace\SDK\Order\Order::getTaxTotal`
- Added `\Wizaplace\SDK\Catalog\DeclinationSummary::isAvailable`
- Added `\Wizaplace\SDK\Catalog\Declination::isAvailable`

## 0.11.2

### New features

- Change HTTP client's user-agent for "Wizaplace-PHP-SDK/"+version
- Added `\Wizaplace\SDK\Basket\BasketService::mergeBaskets`
- Added `\Wizaplace\SDK\Order\OrderItem::getCustomerComment`

## 0.11.1

### New features

- Added `\Wizaplace\SDK\Basket\Basket::getComment`
- Added `\Wizaplace\SDK\Basket\BasketItem::getComment`

## 0.11.0

### BREAKING CHANGES

- `\Wizaplace\SDK\Catalog\CatalogService::getProductById` now takes a string instead of an int.
- `\Wizaplace\SDK\Catalog\Review\ReviewService::getProductReviews` now takes a string instead of an int.
- `\Wizaplace\SDK\Catalog\Review\ReviewService::reviewProduct` now takes a string instead of an int for `$productId`.

### New features

- Added `\Wizaplace\SDK\Catalog\Product::getOtherOffers`

## 0.10.2

### New features

- Added `\Wizaplace\SDK\Basket\BasketService::updateComments`

## 0.10.1

### New features

- Added `\Wizaplace\SDK\Catalog\CatalogService::getBrand`
- Added `\Wizaplace\SDK\Catalog\CatalogService::getBrandFromProductSummary`
- Added `\Wizaplace\SDK\Catalog\CatalogService::getBrandFromProduct`

## 0.10.0

### BREAKING CHANGES

- `\Wizaplace\SDK\Catalog\ProductAttribute::getId` is now nullable (bugfix, as the value was already null in some cases, but it raised a fatal error)
- `\Wizaplace\SDK\Catalog\SearchProductAttribute::getId` is now nullable (bugfix, as the value was already null in some cases, but it raised a fatal error)

### New features

- Added `\Wizaplace\SDK\Catalog\ProductAttribute::getType`

## 0.9.7

### New features

- Added `\Wizaplace\SDK\Basket\BasketService::setUserBasketId`
- Added `\Wizaplace\SDK\Basket\BasketService::getUserBasketId`
- Added `\Wizaplace\SDK\Catalog\Product::getAttachments`

## 0.9.6

### New features

- Added `\Wizaplace\SDK\User\UserService::changePasswordWithRecoveryToken`

## 0.9.5

- Added an optional parameter `$recoverBaseUrl` to `\Wizaplace\SDK\User\UserService::recoverPassword`

## 0.9.4

### Bugfixes

- `CategoryTree` is now sorted by position

## 0.9.3

### New features

- Added `\Wizaplace\SDK\Catalog\ProductSummary::getGeolocation`

## 0.9.2

### New features

- Added `\Wizaplace\SDK\Catalog\ProductAttribute::getValueIds`
- Added `\Wizaplace\SDK\Catalog\AttributeVariant::getDescription`
- Added `\Wizaplace\SDK\Catalog\SearchProductAttribute::getType`
- Added `\Wizaplace\SDK\Catalog\AttributeType::FREE`

## 0.9.1

### New features

- Added `\Wizaplace\SDK\Order\DeclinationOption`
- Added `\Wizaplace\SDK\Order\OrderItem::getDeclinationOptions`
- `\Wizaplace\SDK\User\UpdateUserCommand::setBirthday` now takes a `\DateTimeInterface` instead of a `\DateTime`
- Added `\Wizaplace\SDK\Order\OrderService::sendAfterSalesServiceRequest`
- Added `\Wizaplace\SDK\Catalog\ProductAttribute::getId`

## 0.9.0

### BREAKING CHANGES

- `\Wizaplace\SDK\Translation\TranslationService::pushXliffCatalog` now uses a `$password` parameter instead of the apiClient authentication
- Removed `\Wizaplace\SDK\Catalog\Product::getUrl`
- Removed `\Wizaplace\SDK\Catalog\ProductSummary::getUrl`
- Removed `\Wizaplace\SDK\Basket\BasketItem::getProductUrl`
- Replaced `\Wizaplace\SDK\Catalog\Company` by `\Wizaplace\SDK\Catalog\CompanySummary`

## 0.8.3

### New features

- Added `\Wizaplace\Catalog\Declination::isBrandNew`
- Added `\Wizaplace\Catalog\Product::getVideo`
- Added `\Wizaplace\SDK\User\User::getBirthday`
- Added `\Wizaplace\SDK\User\UpdateUserCommand::setBirthday`
- Added `\Wizaplace\SDK\Catalog\ProductSummary::getShortDescription`

## 0.8.2

### New features

- Added `\Wizaplace\SDK\Catalog\Company::getSlug`
- Added `\Wizaplace\SDK\Catalog\Company::getImage`
- Added `\Wizaplace\SDK\Catalog\Company::isProfessional`
- Added `\Wizaplace\SDK\Catalog\Company::getAverageRating`

## 0.8.1

### New features

- Added `\Wizaplace\SDK\Catalog\CompanySummary::isProfessional`
- Added `\Wizaplace\SDK\Catalog\CompanySummary::getAverageRating`
- Added `\Wizaplace\SDK\MailingList\MailingListService::isSubscribed`
- Added `\Wizaplace\SDK\Catalog\Declination::getCompany`

## 0.8.0

### BREAKING CHANGES

- Base namespace changed from `\Wizaplace` to `Wizaplace\SDK`
- `\Wizaplace\SDK\Favorite\FavoriteService::isInFavorites` is now taking a `string $declinationId` parameter instead of a `int $declinationId` one.
- `\Wizaplace\SDK\Favorite\FavoriteService::addDeclinationToUserFavorites` is now taking a `string $declinationId` parameter instead of a `int $declinationId` one.
- `\Wizaplace\SDK\Favorite\FavoriteService::removeDeclinationToUserFavorites` is now taking a `string $declinationId` parameter instead of a `int $declinationId` one.
- `\Wizaplace\SDK\Order\Order::getStatus` is now returning an `\Wizaplace\SDK\Order\OrderStatus` instead of a string.
- `\Wizaplace\SDK\User\UserService::updateUser` now takes a single `\Wizaplace\SDK\User\UpdateUserCommand` instead of multiple parameters
- `\Wizaplace\SDK\User\User::getBillingAddress` now returns a `\Wizaplace\SDK\User\UserAddress` instead of an array
- `\Wizaplace\SDK\User\User::getShippingAddress` now returns a `\Wizaplace\SDK\User\UserAddress` instead of an array
- `\Wizaplace\SDK\User\UserService::updateUserAdresses` now takes a `\Wizaplace\SDK\User\UpdateUserAddressesCommand`

### New features

- `\Wizaplace\Catalog\ProductReport`'s API is now "fluent"

## 0.7.4

### Bugfixes

- `\Wizaplace\Order\OrderService::getOrderReturns` now uses the right API endpoint (it was systematically throwing 404 errors before)

## 0.7.3

### New features

- Added `\Wizaplace\User\User::getTitle`
- Added (optional) `title` parameter to `\Wizaplace\User\UserService::updateUser`

## 0.7.2

### New features

- Added `\Wizaplace\Basket\BasketItem::getDeclinationOptions`
- Added `\Wizaplace\Catalog\Product::getGeolocation`

## 0.7.1

### New features

- Added `\Wizaplace\Discussion\Message::isAuthor`
- Added `\Wizaplace\Catalog\CatalogService::reportProduct`
- Added `\Wizaplace\Basket\BasketCompany::getSlug`
- Added `\Wizaplace\Catalog\CatalogService::getAttributeVariants`
- Mark `\Wizaplace\Company\CompanyRegistration::getUrl` as deprecated
- Mark `\Wizaplace\Catalog\Product::getUrl` as deprecated
- Mark `\Wizaplace\Catalog\ProductSummary::getUrl` as deprecated

## 0.7.0

### BREAKING CHANGES

- All non-abstract classes are now `final` (cannot be extended). If you have a valid use-case requiring to extend one of these classes, please contact us to discuss it.
- All responses DTOs' constructors are now marked as `@internal`, which means they are not considered a part of the public API anymore. Starting from this release, they may change without being considered as a breaking change.

### New features

- Added `\Wizaplace\Catalog\CompanyDetail::getTerms`
- Added `\Wizaplace\Favorite\FavoriteService::getAll`
- `\Wizaplace\Catalog\Option` and `\Wizaplace\Catalog\OptionVariant` now both implement `\JsonSerializable`

## 0.6.0

### BREAKING CHANGES

- `\Wizaplace\Basket\BasketCompanyGroup::getId` was removed (but it never actually worked)
- `\Wizaplace\User\UserService::updateUser` now has 4 parameters instead of a `\Wizaplace\User\User`
- `\Wizaplace\Order\OrderService::createOrderReturn` now takes a `\Wizaplace\Order\CreateOrderReturn` as parameter instead of the 3 previous parameters

### New features

- Added `\Wizaplace\Catalog\CatalogService::getAttribute`
- Added `\Wizaplace\Catalog\CatalogService::getAttributes`
- Added `\Wizaplace\Catalog\CatalogService::getAttributeVariant`
- Added `\Wizaplace\Company\CompanyService::register`
- Added `\Wizaplace\Discussion\DiscussionService`
- Added `\Wizaplace\Catalog\Product::getOptions`
- Added `\Wizaplace\Catalog\Product::getDeclination`
- Added `\Wizaplace\Catalog\Product::getDeclinationFromOptions`

### Bugfixes

- Fixed `\Wizaplace\Catalog\Category::getParentId` when there is no parent (returns null)
- Fixed `\Wizaplace\Catalog\Category::getImage` when there is no image (returns null)

## 0.5.1

### New features

- Added `\Wizaplace\User\UserService::changePassword`

## 0.5.0

### BREAKING CHANGES

- `ProductAttribute::getValue` can now also return a `string`
- `OrderReturn`'s `timestamp` renamed to `createdAt` (both the getter and the constructor's array key) and expects a RFC3339 date for instanciation
- `ReturnItem`'s `product` renamed to `productName` (both the getter and the constructor's array key)
- `\Wizaplace\Order\OrderService::getReturnTypes` renamed to `getReturnReasons`, and `\Wizaplace\Order\ReturnType` renamed to `ReturnReason`

### New features

- added averageRating to Company
- allow null addresses when creating an user

### Bugfixes

- `ProductAttribute::getValue` does not crash anymore with strings attributes
- `OrderService::getOrderReturn` wasn't working at all. It is now fixed and tested.
- `OrderService::getReturnTypes` wasn't working at all. It is now fixed, tested, and renamed (as seen in the breaking changes list)
- `\Wizaplace\Basket\Payment::getImage` wasn't working at all. It is now fixed and tested.

## 0.4.2

### New features

- added `\Wizaplace\Catalog\CatalogService::getCompanyById`

## 0.4.1

### Bugfixes

- Fix bug with grouped attributes and their `null` value which were triggering a type error

## 0.4.0

### BREAKING CHANGES

- Changed `\DateTime` for `\DateTimeImmutable` in several places
- `BasketShippingGroup::getId` now returns an `int` instead of a `string` (but it was broken before)

### New features

- new `Wizaplace\Catalog\Review\ReviewService`
- new `\Wizaplace\Basket\BasketService::selectShippings`

## 0.3.2

### New features

- Improved documentation

## 0.3.1

### Bugfixes

- Fix `\Wizaplace\Basket\BasketService::create` which was systematically crashing since version 0.3.0

## 0.3.0

### BREAKING CHANGES

- Added new mandatory parameter `$redirectUrl` for `BasketService::checkout`
- Services now need to have an `ApiClient` injected instead of directly a `Guzzle\Client`. Thanks to that, all `ApiKey`s have been removed from methods' signatures (the `ApiClient` will carry the authentication)

### New features

- new `FavoriteService`
- new `TranslationService`
- new `MailingListService`

## 0.2.0

### New features

- CMS Pages API
- CMS Banners API

## 0.1.2

### Bugfixes

- Proper exception thrown in case we receive invalid JSON

## 0.1.1

### Bugfixes

- Fix a type issue in ImageService : d2bdd8b by @sergemazille

## 0.1.0

First release
