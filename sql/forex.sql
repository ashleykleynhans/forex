DROP TABLE IF EXISTS currencies;

CREATE TABLE `currencies` (
  `currency_code` CHAR(3) NOT NULL,
  `currency_name` VARCHAR(100) NOT NULL,
  `currency_surcharge` DECIMAL(3,1) NOT NULL,
  `currency_discount` DECIMAL(4,2) NOT NULL,
  `currency_status` enum('enabled', 'disabled') DEFAULT 'enabled',
  `date_created` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`currency_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `currencies`
  (`currency_code`, `currency_name`, `currency_surcharge`, `currency_discount`)
VALUES
  ('ZAR', 'South African Rands', '7.5', '0.0'),
  ('GBP', 'British Pound', '5.0', '0.0'),
  ('EUR', 'Euro', '5.0', '2.0'),
  ('KES', 'Kenyan Shilling', '2.5', '0.0');

CREATE TABLE `rates` (
  `currency_code` CHAR(3) NOT NULL,
  `exchange_rate` DECIMAL(10,6) NOT NULL,
  `date_created` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`currency_code`),
  FOREIGN KEY (`currency_code`) REFERENCES `currencies` (`currency_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `rates`
  (`currency_code`, `exchange_rate`)
VALUES
  ('ZAR', '13.3054'),
  ('GBP', '0.651178'),
  ('EUR', '0.884872'),
  ('KES', '103.860');

CREATE TABLE `orders` (
  `order_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `currency_code` CHAR(3) NOT NULL,
  `exchange_rate` DECIMAL(10,6) NOT NULL,
  `surcharge_percentage` DECIMAL(3,1) NOT NULL,
  `discount_percentage` DECIMAL(3,1) NOT NULL,
  `currency_amount` DECIMAL(12,2) UNSIGNED NOT NULL,
  `payable_amount` DECIMAL(12,2) UNSIGNED NOT NULL,
  `surcharge_amount` DECIMAL(11,2) UNSIGNED NOT NULL,
  `zar_amount` DECIMAL(12,2) UNSIGNED NOT NULL,
  `discount_amount` DECIMAL(10,2) UNSIGNED NOT NULL,
  `date_created` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  FOREIGN KEY (`currency_code`) REFERENCES `currencies` (`currency_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `order_emails` (
  `email_id` SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
  `currency_code` CHAR(3) NOT NULL,
  `email_address` VARCHAR(255) NOT NULL,
  `email_status` enum('enabled', 'disabled') DEFAULT 'enabled',
  `date_created` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`email_id`),
  FOREIGN KEY (`currency_code`) REFERENCES `currencies` (`currency_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `order_emails`
  (`currency_code`, `email_address`)
VALUES
  ('GBP', 'email@example.com');
