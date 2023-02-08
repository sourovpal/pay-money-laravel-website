
-- Crypto Compare API

INSERT INTO `settings` (`name`, `value`, `type`) VALUES ('crypto_compare_enabled_api', 'Disabled', 'crypto_compare_rate'), ('crypto_compare_api_key', NULL, 'crypto_compare_rate');


-- Request cancel email templates creator and receiver
INSERT INTO `email_templates` (`language_id`, `temp_id`, `subject`, `body`, `lang`, `type`) VALUES ( '1', '32', 'Cancellation of Request Payment', 'Hi {user},\r\n <br><br>\r\n\r\nYour request payment #{uuid} of {amount} has been canceled by {cancelled_by}.\r\n\r\n<br><br>If you have any questions, please feel free to reply to this email.<div><br>Regards,\r\n <br><b>{soft_name}</b>\r\n </div>', 'en', 'email'), ( '2', '32', '', '', 'ar', 'email'), ( '3', '32', '', '', 'fr', 'email'), ( '4', '32', '', '', 'pt', 'email'), ( '5', '32', '', '', 'ru', 'email'), ( '6', '32', '', '', 'es', 'email'), ( '7', '32', '', '', 'tr', 'email'), ( '8', '32', '', '', 'ch', 'email'),
('1', '35', 'Cancellation of Request Payment', 'Hi {creator},\r\n <br><br>\r\n\r\nYour request payment #{uuid} of {amount} has been canceled by {cancelled_by}.\r\n\r\n<br><br>If you have any questions, please feel free to reply to this email.<div><br>Regards,\r\n <br><b>{soft_name}</b>\r\n </div>', 'en', 'sms'), ('2', '35', '', '', 'ar', 'sms'), ('3', '35', '', '', 'fr', 'sms'), ('4', '35', '', '', 'pt', 'sms'), ('5', '35', '', '', 'ru', 'sms'), ('6', '35', '', '', 'es', 'sms'), ('7', '35', '', '', 'tr', 'sms'), ('8', '35', '', '', 'ch', 'sms');

INSERT INTO `email_templates` ( `language_id`, `temp_id`, `subject`, `body`, `lang`, `type`) VALUES ('1', '33', 'Cancellation of Request Payment', 'Hi {user},\r\n <br><br>\r\n\r\nYour request payment #{uuid} of {amount} has been canceled by {cancelled_by}.\r\n\r\n<br><br>If you have any questions, please feel free to reply to this email.<div><br>Regards,\r\n <br><b>{soft_name}</b>\r\n </div>', 'en', 'sms'), ('2', '33', '', '', 'ar', 'sms'), ('3', '33', '', '', 'fr', 'sms'), ('4', '33', '', '', 'pt', 'sms'), ('5', '33', '', '', 'ru', 'sms'), ('6', '33', '', '', 'es', 'sms'), ('7', '33', '', '', 'tr', 'sms'), ('8', '33', '', '', 'ch', 'sms'), 
('1', '34', 'Cancellation of Request Payment', 'Hi {creator},\r\n <br><br>\r\n\r\nYour request payment #{uuid} of {amount} has been canceled by {cancelled_by}.\r\n\r\n<br><br>If you have any questions, please feel free to reply to this email.<div><br>Regards,\r\n <br><b>{soft_name}</b>\r\n </div>', 'en', 'email'), ('2', '34', '', '', 'ar', 'email'), ('3', '34', '', '', 'fr', 'email'), ('4', '34', '', '', 'pt', 'email'), ('5', '34', '', '', 'ru', 'email'), ('6', '34', '', '', 'es', 'email'), ('7', '34', '', '', 'tr', 'email'), ('8', '34', '', '', 'ch', 'email');



