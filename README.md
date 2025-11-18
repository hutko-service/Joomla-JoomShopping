# hutko Payment Module for JoomShopping

## 🇺🇦 Інструкція з встановлення (UA)

### 1. Зайдіть в адміністративну панель вашого сайту.

### 2. Перейдіть у меню:
**Компоненти → JoomShopping → Встановлення та оновлення**

### 3. Встановіть модуль оплати:
- У розділі **Завантажити файл пакета** натисніть **Вибрати файл**
- Оберіть архів із модулем оплати **hutko**
- Натисніть **Завантажити**

### 4. Налаштуйте спосіб оплати:
Перейдіть у:  
**Компоненти → JoomShopping → Опції → Спосіб оплати → hutko → Конфігурація**

Заповніть такі поля:

#### 1) Merchant ID та Секретний ключ (Ключ оплати)
Дані можна знайти у кабінеті hutko:  
**Налаштування мерчанта → Основний профіль → Ключ оплати / ID мерчанта**

#### 2) Статус замовлення для успішних транзакцій
Рекомендовано: **Paid** (замовлення оплачено)

#### 3) Статус замовлення для неуспішних транзакцій
- Щоб скасувати замовлення при відмові від оплати → **Canceled**  
- Щоб дозволити повторну спробу оплати → **Pending**

### 5. Увімкніть спосіб оплати:
- Перейдіть на вкладку **Головна**
- Встановіть позначку **Публікація**, щоб зробити спосіб оплати доступним при оформленні замовлення

### 6. Збережіть зміни, натиснувши **Зберегти**.


---

## 🇺🇸 Installation Guide (EN)

### 1. Open your website’s admin panel.

### 2. Navigate to:
**Components → JoomShopping → Install & Update**

### 3. Install the payment module:
- In **Upload Package File**, click **Choose File**
- Select the archive with the **hutko** payment module
- Click **Upload**

### 4. Configure the payment method:
Go to:  
**Components → JoomShopping → Options → Payment Method → hutko → Configuration**

Fill in the required fields:

#### 1) Merchant ID & Secret Key (Payment Key)
Get these from your hutko dashboard:  
**Merchant Settings → Main Profile → Payment Key / Merchant ID**

#### 2) Order Status for Successful Transactions
Recommended: **Paid**

#### 3) Order Status for Failed Transactions
- To cancel the order when payment is declined → **Canceled**  
- To allow the customer to retry payment → **Pending**

### 5. Enable the payment method:
- Go to the **Main** tab
- Enable **Publication** to make the method available at checkout

### 6. Save your changes by clicking **Save**.
