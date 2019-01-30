# FinReports
Модуль, позволяющий подгружать в карточку картогента информацию о финансовом состоянии контрагента с сервиса testfirm.
Устанавливается как обычный модуль.
В случае ошибки при открытии списков, следует выполнить запрос
INSERT INTO `vtiger_ws_entity` (`id`, `name`, `handler_path`, `handler_class`, `ismodule`) VALUES (NULL, 'FinReports', 'include/Webservices/VtigerModuleOperation.php', 'VtigerModuleOperation', '1');
