# Changelog

<details>
<summary>Unreleased</summary>

### BREAKING CHANGES

### New features

 - Introduce a new detailed exception `\Wizaplace\SDK\Exception\ProductNotFound`
 - Introduce a new detailed exception `\Wizaplace\SDK\Exception\ReviewsAreDisabled`
 - Introduce a new detailed exception `\Wizaplace\SDK\Exception\SenderIsAlsoRecipient`

### Bugfixes

</details>

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
