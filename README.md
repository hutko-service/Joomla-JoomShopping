# Hutko for JoomShopping

JoomShopping payment integration for Joomla 4.

## Installation

1. Open the Joomla administrator area.
2. Go to `Components -> JoomShopping -> Install & Update`.
3. Upload the package zip `pkg_pm_hutko-jed.zip`.
4. Open `Components -> JoomShopping -> Options -> Payment Methods`.
5. Edit `Hutko` and enter the merchant ID and secret key from your Hutko account.
6. Choose the order statuses for successful and failed payments.
7. Publish the payment method.

## JED compliance notes

- The package includes GPL licensing metadata.
- The archive contains a Joomla package manifest `pkg_pm_hutko.xml` so Joomla can detect and install it as a standard package.
- The archive contains a Joomla update server definition in `update.xml`.
- The embedded checkout no longer depends on third-party CDN assets other than the Hutko-hosted checkout library required for payment processing.
- The installer copies the payment files directly into `components/com_jshopping/payments/pm_hutko`.

## Updating

Host both `update.xml` and the release zip `pkg_pm_hutko-jed.zip` at stable public URLs before submitting or updating the JED listing.
