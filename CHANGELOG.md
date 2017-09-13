# Changelog

## Unreleased

### BREAKING CHANGES

- Removed `\Wizaplace\SDK\Catalog\Product::getUrl`
- Removed `\Wizaplace\SDK\Catalog\ProductSummary::getUrl`
- Removed `\Wizaplace\SDK\Basket\BasketItem::getProductUrl`

### New features

### Bugfixes

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
