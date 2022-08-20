<?php

return [
	/*
	ID головной и подведомственных организаций (не клиенты)
	*/
    'main_organizations' => [
        1,
    ],
	// ID - email адреса по умолчанию
    'emailDefault' => 1,
    'adminEmail' => 'admin@test.local',
    'tmpDir' => '@app/storage/temp/',
    'uploadDir' => '@app/storage/files/',
    'debugMailDir' => '@app/storage/mail/',
    'debugCommunication' => false,
    'passwordResetExpired' => 60*60*24*1, // 1 Day
    // SecretKey using for encryption and decryption password
    'secretKey' => 'SECRET_PASS_KEY',
];
