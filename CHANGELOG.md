# Changelog

## 1.115.6

### New features
- Added `Wizaplace\SDK\Catalog\ProductOffer::getPriceTiers`

## 1.115.5

### New features
- Added parameter `nextPaymentAt` to `Wizaplace\SDK\Subscription\SubscriptionUpsertData`

## 1.115.1

### New features
- Added `Wizaplace\SDK\Order\Order::getExtra`
- Added `Wizaplace\SDK\Vendor\Order\Order::getExtra`
- Added `Wizaplace\SDK\Vendor\Order\OrderService::patchExtra`

## 1.114.2

### New features

- Added `Wizaplace\SDK\Order\Refund::isRefundedAfterWithdrawalPeriod`

## 1.114.0

### New features

- Added `Wizaplace\SDK\Exim\EximService::importProductsPrices`
- Added `Wizaplace\SDK\Exim\EximService::importProductsQuantities`

## 1.113.7

### New features

- Added `Wizaplace\SDK\Pim\Product\ProductUpsertData::setCrossedOutPrice`

## 1.113.6

### New features

- Added `Wizaplace\SDK\Vendor\Order\OrderService::shipmentMarkAsDelivered`
- Added `Wizaplace\SDK\Vendor\Order\OrderService::orderMarkAsDelivered`
- Added `Wizaplace\SDK\Vendor\Order\Shipment::getDeliveredDate`

## 1.113.5

### New features

- Added `Wizaplace\SDK\Order\Order::getBasketId`

## 1.113.3

### New features

- Added `Wizaplace\SDK\PIM\Product\ProductListFilter::byCompanyIds`

## 1.112.0

### New features

- Added `Wizaplace\SDK\Vendor\Order\Order::isDoNotCreateInvoice`

## 1.111.31

### New features

- Added `Wizaplace\SDK\ApiClient::logout`

## 1.111.28

### New features

- Added `Wizaplace\SDK\Pim\Product\ProductAttachment::getPublicUrl`
- Added `Wizaplace\SDK\Pim\Product\ProductAttachment::getOriginalUrl`

## 1.111.2

### New features

- Added `Wizaplace\SDK\Vendor\Order\OrderListFilter::byItemPerPage`
- Added `wizaplace\SDK\Vendor\Order\OrderListFilter::byPage`

## 1.111.0

### New features

- Added `Wizaplace\SDK\Currency\Currency::getUpdatedAt`

## 1.110.19

### New features

- Added `Wizaplace\SDK\Order\Order::getBalance`
- Added `Wizaplace\SDK\Vendor\Order\Order::getBalance`
- Added `Wizaplace\SDK\Vendor\Order\OrderSummary::getBalance`

## 1.110.1

### New features

- Added `Wizaplace\SDK\Vendor\Order\Refund::getUserId`

## 1.109.0

## Project Address Book

### New features

- Added `Wizaplace\SDK\User\UserAddress::getId`
- Added `Wizaplace\SDK\User\UserAddress::getLabel`
- Added `Wizaplace\SDK\User\UserAddress::getComment`
- Added `Wizaplace\SDK\User\AddressBook::getTitle`
- Added `Wizaplace\SDK\User\AddressBook::getFirstName`
- Added `Wizaplace\SDK\User\AddressBook::getLastName`
- Added `Wizaplace\SDK\User\AddressBook::getCompany`
- Added `Wizaplace\SDK\User\AddressBook::getPhone`
- Added `Wizaplace\SDK\User\AddressBook::getAddress`
- Added `Wizaplace\SDK\User\AddressBook::getAddressSecondLine`
- Added `Wizaplace\SDK\User\AddressBook::getZipCode`
- Added `Wizaplace\SDK\User\AddressBook::getCity`
- Added `Wizaplace\SDK\User\AddressBook::getCountry`
- Added `Wizaplace\SDK\User\AddressBook::getDivisionCode`
- Added `Wizaplace\SDK\User\AddressBook::getId`
- Added `Wizaplace\SDK\User\AddressBook::setId`
- Added `Wizaplace\SDK\User\AddressBook::getComment`
- Added `Wizaplace\SDK\User\AddressBook::setComment`
- Added `Wizaplace\SDK\User\AddressBook::getLabel`
- Added `Wizaplace\SDK\User\AddressBook::setLabel`
- Added `Wizaplace\SDK\User\AddressBook::toArray`
- Added `Wizaplace\SDK\User\AddressBookService::createAddressInAddressBook`
- Added `Wizaplace\SDK\User\AddressBookService::listAddressBook`
- Added `Wizaplace\SDK\User\AddressBookService::delete`
- Added `Wizaplace\SDK\User\UpdateUserAddressCommand::setId`
- Added `Wizaplace\SDK\User\UpdateUserAddressCommand::getId`
- Added `Wizaplace\SDK\User\UpdateUserAddressCommand::setLabel`
- Added `Wizaplace\SDK\User\UpdateUserAddressCommand::getLabel`
- Added `Wizaplace\SDK\User\UpdateUserAddressCommand::setComment`
- Added `Wizaplace\SDK\User\UpdateUserAddressCommand::getComment`
- Added `Wizaplace\SDK\Basket\BasketService::chooseShippingAddressAction`
- Added `Wizaplace\SDK\Basket\Address::getLabel`
- Added `Wizaplace\SDK\Basket\Address::getComment`
- Added `Wizaplace\SDK\Basket\BasketService::chooseBillingAddressAction`
- Added `Wizaplace\SDK\Order\Address::getLabel`
- Added `Wizaplace\SDK\Order\Address::getComment`
- Added `Wizaplace\SDK\User\AddressBookService::copyAddressInAddressBook`
- Added `Wizaplace\SDK\Company\DivisionService`
- Added `Wizaplace\SDK\Division\DivisionSettings`
- Added `Wizaplace\SDK\Division\DivisionsTreeFilters`
- Added `Wizaplace\SDK\Division\DivisionsTreeTrait`
- Added `Wizaplace\SDK\Pim\Product\DivisionService`
- Updated `Wizaplace\SDK\Division\Division`
- Updated `Wizaplace\SDK\Division\DivisionService`
- Removed `Wizaplace\SDK\Division\DivisionCompany`
- Removed `Wizaplace\SDK\Pim\Product\ProductService` divisions methods
- Removed `Wizaplace\SDK\Company\CompanyService` divisions methods
- Removed `Wizaplace\SDK\Division\DivisionUtils`

## 1.108.32

### Bugfixes

- `Wizaplace\SDK\Pim\Product\ProductUpsertData`: fixes the creation of product with type "service" when using product template.


## 1.108.26

### New features

- Added `Wizaplace\SDK\Pim\Category\CategoryService.php::getCategory`

## 1.108.22

### New features

- Added `Wizaplace\SDK\Vendor\Order\Order::isRefunded`
- Added `Wizaplace\SDK\Vendor\Order\OrderSummary::isRefunded`
- Added `Wizaplace\SDK\Order\Order::isRefunded`

### New features

- Added `Wizaplace\SDK\Vendor\Order\Refund::getUserId`

## 1.108.1

### New features

- Added `Wizaplace\SDK\Catalog\CatalogSortCriteria.php`
- Added `Wizaplace\SDK\SortDirection.php`
- Updated `Wizaplace\SDK\Catalog\CatalogServiceInterface.php::getCategoryTree` Add sort with Id, Name, Position and ProductCount

## 1.106.0

### New features

- Added `Wizaplace\SDK\Vendor\Order\OrderService::postOrderAttachment`
- Added `Wizaplace\SDK\Vendor\Order\OrderService::getOrderAttachment`
- Added `Wizaplace\SDK\Vendor\Order\OrderService::listOrderAttachment`
- Added `Wizaplace\SDK\Vendor\Order\OrderService::updateOrderAttachment`
- Added `Wizaplace\SDK\Vendor\Order\OrderService::deleteOrderAttachment`
- Added `Wizaplace\SDK\Vendor\Order\OrderAttachment`
- Added `Wizaplace\SDK\Vendor\Order\OrderAttachmentFilter`
- Added `Wizaplace\SDK\Vendor\Order\OrderAttachmentType`

## 1.104.8

### New features

- Update `Wizaplace/SDK/Seo/SeoService.php::listSlugs` add 2 optional params `$offset` and `$limit`

## 1.102.2

### New features

- Added `Wizaplace\SDK\Exception\FileNotFound`
- Added `Wizaplace\SDK\Exim\EximService`

### Bugfixes

- `Wizaplace\SDK\User\UserService::registerWithFullInfos`: fix user title

## 1.101.4

### New features

- Added `Wizaplace\SDK\Company\CompanyPatchCommand`
- Added `Wizaplace\SDK\Company\CompanyStatus`
- Added `Wizaplace\SDK\Company\Company::getStatus`
- Added `Wizaplace\SDK\Company\CompanyService::patch`

## 1.101.0

### New features

- Added `Wizaplace\SDK\Basket\PaymentInformation::getParentOrderId`
- Added `Wizaplace\SDK\Catalog\ProductSummary::getMaxPriceAdjustment`
- Added `Wizaplace\SDK\Catalog\Declination::getMaxPriceAdjustment`

## 1.100.26

### New features

- Added `Wizaplace\SDK\Pim\Product\ProductDeclination::getSupplierReference()`
- Added `Wizaplace\SDK\Pim\Product\ProductDeclinationUpsertData::getSupplierReference()`
- Added `Wizaplace\SDK\Pim\Product\ProductDeclinationUpsertData::setSupplierReference()`

## 1.100.24

### New features

- Added `Wizaplace\SDK\Order\DeclinationOption::getCode`

## 1.100.20

- Added `Wizaplace\SDK\Catalog\CatalogSortCriteria.php`
- Added `Wizaplace\SDK\Catalog\CatalogSortDirection.php`
- Updated `Wizaplace\SDK\Catalog\CatalogServiceInterface.php::getCategoryTree` Add sort with Id, Name, Position and ProductCount

### Deprecated

- `Wizaplace\SDK\Basket\BasketService::addProductToBasket` use `addProduct` instead

### New features

- Added `Wizaplace\SDK\Basket\BasketService::addProduct`
- Added `Wizaplace\SDK\Vendor\Order\OrderAddress::getTitle()`

## 1.100.13

### New features

- Added `Wizaplace\SDK\User\User::getLanguage`
- Added `Wizaplace\SDK\User\RegisterUserCommand::getLanguage`
- Added `Wizaplace\SDK\User\RegisterUserCommand::setLanguage`
- Added `Wizaplace\SDK\User\UpdateUserCommand::getLanguage`
- Added `Wizaplace\SDK\User\UpdateUserCommand::setLanguage`

## 1.100.6

### New features

- Added `Wizaplace\SDK\Catalog\Product::getMaxPriceAdjustment`

## 1.100.4

### New features

- Added `Wizaplace\SDK\User\UserService::registerPartially`

## 1.98.3

### Bugfixes

- Added `Wizaplace\SDK\Catalog\Option::getCode`
- Added `Wizaplace\SDK\Catalog\DeclinationOption::getCode`

## 1.97.0

### New features

- Added `Wizaplace\SDK\User\User::getExternalIdentifier`
- Added `Wizaplace\SDK\User\RegisterUserCommand::getExternalIdentifier`
- Added `Wizaplace\SDK\User\RegisterUserCommand::setExternalIdentifier`
- Added `Wizaplace\SDK\User\UpdateUserCommand::getExternalIdentifier`
- Added `Wizaplace\SDK\User\UpdateUserCommand::setExternalIdentifier`
- Added `Wizaplace\SDK\User\User::getIsProfessional`
- Added `Wizaplace\SDK\User\RegisterUserCommand::getIsProfessional`
- Added `Wizaplace\SDK\User\RegisterUserCommand::setIsProfessional`
- Added `Wizaplace\SDK\User\UpdateUserCommand::getIsProfessional`
- Added `Wizaplace\SDK\User\UpdateUserCommand::setIsProfessional`
- Added `Wizaplace\SDK\User\User::getIntraEuropeanCommunityVAT`
- Added `Wizaplace\SDK\User\RegisterUserCommand::getIntraEuropeanCommunityVAT`
- Added `Wizaplace\SDK\User\RegisterUserCommand::setIntraEuropeanCommunityVAT`
- Added `Wizaplace\SDK\User\UpdateUserCommand::getIntraEuropeanCommunityVAT`
- Added `Wizaplace\SDK\User\UpdateUserCommand::setIntraEuropeanCommunityVAT`
- Added `Wizaplace\SDK\User\User::getCompany`
- Added `Wizaplace\SDK\User\RegisterUserCommand::getCompany`
- Added `Wizaplace\SDK\User\RegisterUserCommand::setCompany`
- Added `Wizaplace\SDK\User\UpdateUserCommand::getCompany`
- Added `Wizaplace\SDK\User\UpdateUserCommand::setCompany`
- Added `Wizaplace\SDK\User\User::getJobTitle`
- Added `Wizaplace\SDK\User\RegisterUserCommand::getJobTitle`
- Added `Wizaplace\SDK\User\RegisterUserCommand::setJobTitle`
- Added `Wizaplace\SDK\User\UpdateUserCommand::getJobTitle`
- Added `Wizaplace\SDK\User\UpdateUserCommand::setJobTitle`
- Added `Wizaplace\SDK\User\User::getComment`
- Added `Wizaplace\SDK\User\RegisterUserCommand::getComment`
- Added `Wizaplace\SDK\User\RegisterUserCommand::setComment`
- Added `Wizaplace\SDK\User\UpdateUserCommand::getComment`
- Added `Wizaplace\SDK\User\UpdateUserCommand::setComment`
- Added `Wizaplace\SDK\User\User::getLegalIdentifier`
- Added `Wizaplace\SDK\User\RegisterUserCommand::getLegalIdentifier`
- Added `Wizaplace\SDK\User\RegisterUserCommand::setLegalIdentifier`
- Added `Wizaplace\SDK\User\UpdateUserCommand::getLegalIdentifier`
- Added `Wizaplace\SDK\User\UpdateUserCommand::setLegalIdentifier`
- Added `Wizaplace\SDK\User\User::getLoyaltyIdentifier`
- Added `Wizaplace\SDK\User\RegisterUserCommand::getLoyaltyIdentifier`
- Added `Wizaplace\SDK\User\RegisterUserCommand::setLoyaltyIdentifier`
- Added `Wizaplace\SDK\User\UpdateUserCommand::getLoyaltyIdentifier`
- Added `Wizaplace\SDK\User\UpdateUserCommand::setLoyaltyIdentifier`

## 1.96.0

### New features

- Added `Wizaplace\SDK\Pim\Product\PriceTier`
- Added `Wizaplace\SDK\Pim\Product\PriceTier::getLowerLimit`
- Added `Wizaplace\SDK\Pim\Product\PriceTier::setLowerLimit`
- Added `Wizaplace\SDK\Pim\Product\PriceTier::getPrice`
- Added `Wizaplace\SDK\Pim\Product\PriceTier::setPrice`
- Added `Wizaplace\SDK\Pim\Product\ExtendedPriceTier`
- Added `Wizaplace\SDK\Pim\Product\ExtendedPriceTier::getTaxes`
- Added `Wizaplace\SDK\Pim\Product\ExtendedPriceTier::setTaxes`
- Added `Wizaplace\SDK\Pim\Product\ExtendedPriceTier::getPriceIncludeTax`
- Added `Wizaplace\SDK\Pim\Product\ExtendedPriceTier::setPriceIncludeTax`
- Added `Wizaplace\SDK\Pim\Product\ExtendedPriceTier::getPriceExcludingTaxes`
- Added `Wizaplace\SDK\Pim\Product\ExtendedPriceTier::setPriceExcludingTaxes`
- Updated `Wizaplace\SDK\Catalog\Declination` Add 'priceTiers' to Declination
- Added `Wizaplace\SDK\Catalog\Declination::addPriceTier`
- Added `Wizaplace\SDK\Catalog\Declination::getPriceTiers`
- Updated `Wizaplace\SDK\Catalog\Product` Add 'priceTiers' to Product
- Added `Wizaplace\SDK\Catalog\Product::addPriceTier`
- Added `Wizaplace\SDK\Catalog\Product::getPriceTier`
- Updated `Wizaplace\SDK\Pim\Product\ProductDeclination` Add 'priceTiers' to ProductDeclination
- Added `Wizaplace\SDK\Pim\Product\ProductDeclination::addPriceTier`
- Added `Wizaplace\SDK\Pim\Product\ProductDeclination::getPriceTiers`
- Updated `Wizaplace\SDK\Pim\Product\ProductDeclinationUpsertData` Add 'priceTiers' to ProductDeclinationUpsertData
- Added `Wizaplace\SDK\Pim\Product\ProductDeclinationUpsertData::setPriceTiers`
- Updated `Wizaplace\SDK\Pim\Product\ProductDeclinationUpsertData::loadValidatorMetadata`
- Updated `Wizaplace\SDK\Pim\Product\ProductDeclinationUpsertData::toArray`

## 1.94.0

### New features

- Added `Wizaplace\SDK\Commission\CommissionService::updateCompanyCommission`
- Added `Wizaplace\SDK\Commission\CommissionService::updateCategoryCommission`
- Added `Wizaplace\SDK\Commission\CommissionService::updateMarketplaceCommission`
- Added `Wizaplace\SDK\Commission\CommissionService::addCompanyCommission`
- Added `Wizaplace\SDK\Commission\CommissionService::addCategoryCommission`
- Added `Wizaplace\SDK\Commission\CommissionService::addMarketplaceCommission`
- Added `Wizaplace\SDK\Commission\CommissionService::getCommissions`
- Added `Wizaplace\SDK\Commission\CommissionService::getMarketplaceCommission`
- Added `Wizaplace\SDK\Commission\CommissionService::getCommission`
- Added `Wizaplace\SDK\Commission\CommissionService::getCategoryCommission`
- Added `Wizaplace\SDK\Commission\CommissionService::getCompanyCommission`
- Added `Wizaplace\SDK\Commission\CommissionService::deleteCommission`
- Added `Wizaplace\SDK\Vendor\Attribute\AttributeService::getAttribute`
- Added `Wizaplace\SDK\Basket\BasketService::deleteUserBasket`
- Added `Wizaplace\SDK\Pim\Product\Product::isSubscription`
- Added `Wizaplace\SDK\Pim\Product\Product::isRenewable`
- Added `Wizaplace\SDK\Catalog\Declination::isSubscription`
- Added `Wizaplace\SDK\Catalog\Declination::setIsSubscription`
- Added `Wizaplace\SDK\Catalog\Declination::isRenewable`
- Added `Wizaplace\SDK\Catalog\Declination::setIsRenewable`
- Added `Wizaplace\SDK\Catalog\Product::isSubscription`
- Added `Wizaplace\SDK\Catalog\Product::isRenewable`
- Added `Wizaplace\SDK\Pim\Option\Option::isSystem`
- Added `Wizaplace\SDK\Pim\Option\Option::getCode`
- Added `Wizaplace\SDK\Pim\Option\OptionService::getProductOptions`
- Added `Wizaplace\SDK\Pim\Option\OptionService::getOption`
- Added `Wizaplace\SDK\Vendor\Order\Order::isPaid`
- Added `Wizaplace\SDK\Vendor\Order\Order::getSubscriptionId`
- Added `Wizaplace\SDK\Vendor\Order\OrderService::getSubscriptions`
- Added `Wizaplace\SDK\Vendor\Order\OrderSummary::isPaid`
- Added `Wizaplace\SDK\Vendor\Order\OrderSummary::getSubscriptionId`
- Added `Wizaplace\SDK\Order\Order::isPaid`
- Added `Wizaplace\SDK\Order\Order::getSubscriptionId`
- Added `Wizaplace\SDK\Order\OrderService::getSubscriptions`
- Added `Wizaplace\SDK\Company\CompanyService::listSubscriptionsBy`
- Added `Wizaplace\SDK\Subscription\SubscriptionFilter::getFilters`
- Added `Wizaplace\SDK\Subscription\SubscriptionFilter::getLimit`
- Added `Wizaplace\SDK\Subscription\SubscriptionFilter::setLimit`
- Added `Wizaplace\SDK\Subscription\SubscriptionFilter::getOffset`
- Added `Wizaplace\SDK\Subscription\SubscriptionFilter::setOffset`
- Added `Wizaplace\SDK\Subscription\SubscriptionFilter::getStatus`
- Added `Wizaplace\SDK\Subscription\SubscriptionFilter::setStatus`
- Added `Wizaplace\SDK\Subscription\SubscriptionFilter::getCompanyId`
- Added `Wizaplace\SDK\Subscription\SubscriptionFilter::setCompanyId`
- Added `Wizaplace\SDK\Subscription\SubscriptionFilter::getUserId`
- Added `Wizaplace\SDK\Subscription\SubscriptionFilter::setUserId`
- Added `Wizaplace\SDK\Subscription\SubscriptionFilter::getProductId`
- Added `Wizaplace\SDK\Subscription\SubscriptionFilter::setProductId`
- Added `Wizaplace\SDK\Subscription\SubscriptionFilter::getCommitmentEndBefore`
- Added `Wizaplace\SDK\Subscription\SubscriptionFilter::setCommitmentEndBefore`
- Added `Wizaplace\SDK\Subscription\SubscriptionFilter::getCommitmentEndAfter`
- Added `Wizaplace\SDK\Subscription\SubscriptionFilter::setCommitmentEndAfter`
- Added `Wizaplace\SDK\Subscription\SubscriptionFilter::getIsAutorenew`
- Added `Wizaplace\SDK\Subscription\SubscriptionFilter::setIsAutorenew`
- Added `Wizaplace\SDK\Subscription\SubscriptionStatus::ACTIVE`
- Added `Wizaplace\SDK\Subscription\SubscriptionStatus::DEFAULTED`
- Added `Wizaplace\SDK\Subscription\SubscriptionStatus::DISABLED`
- Added `Wizaplace\SDK\Subscription\SubscriptionStatus::FINISHED`
- Added `Wizaplace\SDK\Subscription\SubscriptionStatus::SUSPENDED`
- Added `Wizaplace\SDK\Subscription\SubscriptionSummary::getId`
- Added `Wizaplace\SDK\Subscription\SubscriptionSummary::getUserId`
- Added `Wizaplace\SDK\Subscription\SubscriptionSummary::getCompanyId`
- Added `Wizaplace\SDK\Subscription\SubscriptionSummary::getCardId`
- Added `Wizaplace\SDK\Subscription\SubscriptionSummary::getFirstOrderId`
- Added `Wizaplace\SDK\Subscription\SubscriptionSummary::getName`
- Added `Wizaplace\SDK\Subscription\SubscriptionSummary::getStatus`
- Added `Wizaplace\SDK\Subscription\SubscriptionSummary::getPrice`
- Added `Wizaplace\SDK\Subscription\SubscriptionSummary::isAutorenew`
- Added `Wizaplace\SDK\Subscription\SubscriptionSummary::getCommitmentPeriod`
- Added `Wizaplace\SDK\Subscription\SubscriptionSummary::getPaymentFrequency`
- Added `Wizaplace\SDK\Subscription\SubscriptionSummary::getCreatedAt`
- Added `Wizaplace\SDK\Subscription\SubscriptionSummary::getNextPaymentAt`
- Added `Wizaplace\SDK\Subscription\SubscriptionSummary::getCommitmentEndAt`
- Added `Wizaplace\SDK\Subscription\Subscription::getItems`
- Added `Wizaplace\SDK\Subscription\Subscription::getTaxes`
- Added `Wizaplace\SDK\Subscription\SubscriptionItem::getCategoryId`
- Added `Wizaplace\SDK\Subscription\SubscriptionItem::getProductId`
- Added `Wizaplace\SDK\Subscription\SubscriptionItem::getProductCode`
- Added `Wizaplace\SDK\Subscription\SubscriptionItem::getProductName`
- Added `Wizaplace\SDK\Subscription\SubscriptionItem::isProductIsRenewable`
- Added `Wizaplace\SDK\Subscription\SubscriptionItem::getDeclinationId`
- Added `Wizaplace\SDK\Subscription\SubscriptionItem::getUnitPrice`
- Added `Wizaplace\SDK\Subscription\SubscriptionItem::getQuantity`
- Added `Wizaplace\SDK\Subscription\SubscriptionItem::getTotalPrice`
- Added `Wizaplace\SDK\Subscription\SubscriptionTax::getTaxId`
- Added `Wizaplace\SDK\Subscription\SubscriptionTax::getTaxName`
- Added `Wizaplace\SDK\Subscription\SubscriptionTax::getAmount`
- Added `Wizaplace\SDK\Subscription\SubscriptionService::listBy`
- Added `Wizaplace\SDK\Subscription\SubscriptionService::getSubscription`
- Added `Wizaplace\SDK\Subscription\SubscriptionService::getItems`
- Added `Wizaplace\SDK\Subscription\SubscriptionService::getTaxes`
- Added `Wizaplace\SDK\Subscription\SubscriptionService::getOrders`
- Added `Wizaplace\SDK\Subscription\SubscriptionService::patchSubscription`
- Added `Wizaplace\SDK\PaginatedData::getLimit`
- Added `Wizaplace\SDK\PaginatedData::getOffset`
- Added `Wizaplace\SDK\PaginatedData::getTotal`
- Added `Wizaplace\SDK\PaginatedData::getItems`
- Added `Wizaplace\SDK\Price::getExcludingTaxes`
- Added `Wizaplace\SDK\Price::getIncludingTaxes`
- Added `Wizaplace\SDK\Price::getTaxes`
- Added `Wizaplace\SDK\User\UserService::listSubscriptionsBy`
- Added `Wizaplace\SDK\CreditCard\CreditCard::getId`
- Added `Wizaplace\SDK\CreditCard\CreditCard::getUserId`
- Added `Wizaplace\SDK\CreditCard\CreditCard::getBrand`
- Added `Wizaplace\SDK\CreditCard\CreditCard::getPan`
- Added `Wizaplace\SDK\CreditCard\CreditCard::getHolder`
- Added `Wizaplace\SDK\CreditCard\CreditCard::getExpiryMonth`
- Added `Wizaplace\SDK\CreditCard\CreditCard::getExpiryYear`
- Added `Wizaplace\SDK\CreditCard\CreditCard::getIssuer`
- Added `Wizaplace\SDK\CreditCard\CreditCard::getCountry`
- Added `Wizaplace\SDK\CreditCard\CreditCard::getCreatedAt`
- Added `Wizaplace\SDK\CreditCard\CreditCardService::getCreditCards`
- Added `Wizaplace\SDK\CreditCard\CreditCardService::getCreditCard`
- Added `Wizaplace\SDK\CreditCard\CreditCardService::getRegistrationUrl`
- Added `Wizaplace\SDK\Payment\PaymentService::getPaymentMethods`
- Added `Wizaplace\SDK\Catalog\ProductFilter::IS_SUBSCRIPTION`
- Added `Wizaplace\SDK\Catalog\ProductFilter::getIsSubscription`
- Added `Wizaplace\SDK\Catalog\ProductFilter::setIsSubscription`
- Added `Wizaplace\SDK\Pim\Option\SystemOption::PAYMENT_FREQUENCY`
- Added `Wizaplace\SDK\Pim\Option\SystemOption::COMMITMENT_PERIOD`

## 1.88.14

### New features

- Added `Wizaplace\SDK\Vendor\Attribute\AttributeService::getAttribute`

## 1.80.0

### New features

- Added `Wizaplace\SDK\Vendor\Promotion\MarketplacePromotion`
- Added `Wizaplace\SDK\Vendor\Promotion\MarketplacePromotionService`
- Added `Wizaplace\SDK\Vendor\Promotion\MarketplacePromotionsList`
- Added `Wizaplace\SDK\Vendor\Promotion\SaveMarketplacePromotionCommand`
- Updated `Wizaplace\SDK\Vendor\Promotion\BasketRuleType` Add new ruleType
- Added `Wizaplace\SDK\Vendor\Promotion\Rules\BasketPriceInferiorOrEqualToRule`
- Added `Wizaplace\SDK\Vendor\Promotion\Rules\BasketPriceSuperiorOrEqualToRule`
- Added `Wizaplace\SDK\Vendor\Order::getMarketplaceDiscountTotal`
- Added `Wizaplace\SDK\Vendor\Order::getCustomerTotal`
- Added `Wizaplace\SDK\Vendor\Order::getTransactions`
- Added `Wizaplace\SDK\Transaction\Transaction`
- Added `Wizaplace\SDK\Transaction\TransactionType`
- Added `Wizaplace\SDK\Transaction\TransactionStatus`
- Added `Wizaplace\SDK\Vendor\Order\OrderService::getTransactions`
- Added `Wizaplace\SDK\Basket\Basket\BasketService::getTotalMarketplaceDiscount`
- Added `Wizaplace\SDK\User::getPendingCompanyId`

## 1.70.8

### Bugfixes

- Fixed `Vendor/Promotion/BasketPromotion::getRule` can return null

### New features

- Added `Wizaplace\SDK\Catalog\Product::isMVP` to know if a product was a Multi-Vendor product or not
- Added `Wizaplace\SDK\Basket\Payment::getExternalReference`
- Added `Wizaplace\SDK\Order\Payment::getExternalReference`
- Added `Wizaplace\SDK\Catalog::getProductsByMvpId`
- Added parameter `bool $allowMvp = true` to `Wizaplace\SDK\Catalog::getProductsBy*` methods
- Added `\Wizaplace\SDK\Order\OrderService::createOrderAdjustment`
- Added `\Wizaplace\SDK\Vendor\Order\OrderService::createOrderAdjustment`
- Added `\Wizaplace\SDK\Order\OrderService::getAdjustments`
- Added `\Wizaplace\SDK\Vendor\Order\OrderService::getAdjustments`
- Added `\Wizaplace\SDK\Order\OrderAdjustment`
- Added `\Wizaplace\SDK\Pim\Product\ProductSummary::getMaxPriceAdjustment`
- Added `\Wizaplace\SDK\PIM\Product\ProductUpsertData::setMaxPriceAdjustment`

## 1.67.2

### Bugfixes

- Added `Wizaplace\SDK\Pim\Product\ProductService::addAttachments`
- Added `Wizaplace\SDK\Pim\Product\ProductService::removeAttachment`
- Added `Wizaplace\SDK\Catalog\CatalogService::getProductAttachment`

## 1.66.0

### New features

- Added `Wizaplace\SDK\Vendor\Order\Order::getDetails`
- Added `Wizaplace\SDK\Vendor\Order\OrderService::setOrderDetails`
- Added `alt` property to `\Wizaplace\SDK\Cms\Banner`

### Bugfixes

- Updated `Wizaplace\SDK\Currency\CurrencyService::getAll` Authentication anonymously authorized
- Updated `Wizaplace\SDK\Currency\CurrencyService::getByFilters` Authentication anonymously authorized
- Updated `Wizaplace\SDK\Currency\CurrencyService::getCurrency` Authentication anonymously authorized
- Updated `Wizaplace\SDK\Currency\CurrencyService::getCountries` Authentication anonymously authorized
- Fixed `Wizaplace\SDK\Catalog\Declination::getShippings` Only available for a product, for a MVP we're not able to know the shippings

## 1.63.0

### New features

- Added `Wizaplace\SDK\Currency\CurrencyService::getAll`
- Added `Wizaplace\SDK\Currency\CurrencyService::getByFilters`
- Added `Wizaplace\SDK\Currency\CurrencyService::getByCountryCode`
- Added `Wizaplace\SDK\Currency\CurrencyService::getCurrency`
- Added `Wizaplace\SDK\Currency\CurrencyService::getCountries`
- Added `Wizaplace\SDK\Currency\CurrencyService::addCountry`
- Added `Wizaplace\SDK\Currency\CurrencyService::removeCountry`
- Added `Wizaplace\SDK\Currency\CurrencyService::updateCurrency`
- Added `\Wizaplace\SDK\User\UserService::patchUser`
- Updated `\Wizaplace\SDK\User\User` Add 'currencyCode' to user profile
- Updated `\Wizaplace\SDK\User\UserService::updateUser` Add 'currencyCode' property

## 1.62.0

### New features

- Added `\Wizaplace\SDK\Seo\Metadata.php`
- Added `meta` => [`title`, `description`, `keywords`] to `\Wizaplace\SDK\Company\CompanyService.php::update()`
- Added `metaTitle`, `metaDescription` and `metaKeywords` with getters and setters to `\Wizaplace\SDK\Company\CompanyUpdateCommand.php`
- Added `metadata` with getter and setter to `\Wizaplace\SDK\Company\Company.php` and `\Wizaplace\SDK\Catalog\CompanyDetail.php`

## 1.61.0

### New features

- Added `\Wizaplace\SDK\Catalog\Product::getProductTemplateType`
- Added `\Wizaplace\SDK\Pim\Product\Product::getProductTemplateType`
- Added `\Wizaplace\SDK\Pim\Product\ProductUpsertData::setProductTemplateType`

## 1.60.0

### New features

- Added `lastStatusChange` property to `Wizaplace\SDK\Vendor\Order` and `Wizaplace\SDK\Vendor\OrderSummary`
- Added `\Wizaplace\SDK\Vendor\Order\OrderListFilter`
- Added `\Wizaplace\SDK\Vendor\Order\OrderListFilter::byCategoryIds`
- Added `\Wizaplace\SDK\Vendor\Order\OrderListFilter::byLastStatusChangeIsAfter`
- Added `\Wizaplace\SDK\Vendor\Order\OrderListFilter::byLastStatusChangeIsBefore`
- Updated `\Wizaplace\SDK\Vendor\Order\OrderService::listOrders`


## 1.57.0

### Bugfixes

- Added `Iban` and `Bic` to data sent by `Wizaplace\SDK\Company\CompanyService::unauthenticatedRegister()`
- Fixed `\Wizaplace\SDK\Company\Company::__construct` with NAF Code `null` in constructor

## 1.56.0

### New features

- Updated `\Wizaplace\SDK\Discussion\DiscussionService::submitContactRequest` add 3 optional params `$recipientEmail`, `$attachmentsUrls` and `$files`

## 1.55.1

### New features

- Added `\Wizaplace\SDK\AuthLog\AuthLogService::get`
- Added `\Wizaplace\SDK\AuthLog\AuthLogService::search`

### Bugfixes

- Fixed missing Order status in `Wizaplace\SDK\Vendor\Order\OrderStatus` and `Wizaplace\SDK\Order\OrderStatus`

## 1.52.0

### New features

- Fixed `\Wizaplace\SDK\Catalog\Product` shippings into declination
- Added `\Wizaplace\SDK\Company\AbstractCompanyRegistration::getNafCode`
- Added `\Wizaplace\SDK\Company\AbstractCompanyRegistration::setNafCode`
- Added `\Wizaplace\SDK\Company\Company::getNafCode`
- Added `\Wizaplace\SDK\Company\CompanyUpdateCommand::getNafCode`
- Added `\Wizaplace\SDK\Company\CompanyUpdateCommand::setNafCode`

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
- Added `\Wizaplace\SDK\Language\Language`
- Added `\Wizaplace\SDK\Language\LanguageService::getAllLanguages`

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
