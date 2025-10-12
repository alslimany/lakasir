<?php

declare(strict_types=1);

return [
    'accepted' => 'يجب قبول :attribute.',
    'accepted_if' => 'يجب قبول :attribute في حالة :other يساوي :value.',
    'active_url' => 'حقل :attribute لا يُمثّل رابطًا صحيحًا.',
    'after' => 'يجب على حقل :attribute أن يكون تاريخًا لاحقًا للتاريخ :date.',
    'after_or_equal' => 'حقل :attribute يجب أن يكون تاريخاً لاحقاً أو مطابقاً للتاريخ :date.',
    'alpha' => 'يجب أن لا يحتوي حقل :attribute سوى على حروف.',
    'alpha_dash' => 'يجب أن لا يحتوي حقل :attribute سوى على حروف، أرقام ومطّات.',
    'alpha_num' => 'يجب أن يحتوي حقل :attribute على حروفٍ وأرقامٍ فقط.',
    'array' => 'يجب أن يكون حقل :attribute ًمصفوفة.',
    'before' => 'يجب على حقل :attribute أن يكون تاريخًا سابقًا للتاريخ :date.',
    'before_or_equal' => 'حقل :attribute يجب أن يكون تاريخا سابقا أو مطابقا للتاريخ :date.',
    'between' => [
        'array' => 'يجب أن يحتوي حقل :attribute على عدد من العناصر بين :min و :max.',
        'file' => 'يجب أن يكون حجم ملف حقل :attribute بين :min و :max كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة حقل :attribute بين :min و :max.',
        'string' => 'يجب أن يكون عدد حروف نّص حقل :attribute بين :min و :max.',
    ],
    'boolean' => 'يجب أن تكون قيمة حقل :attribute إما true أو false.',
    'confirmed' => 'حقل التأكيد غير مُطابق للحقل :attribute.',
    'current_password' => 'كلمة المرور غير صحيحة.',
    'date' => 'حقل :attribute ليس تاريخًا صحيحًا.',
    'date_equals' => 'يجب أن يكون حقل :attribute مطابقاً للتاريخ :date.',
    'date_format' => 'لا يتوافق حقل :attribute مع الشكل :format.',
    'declined' => 'يجب رفض :attribute.',
    'declined_if' => 'يجب رفض :attribute عندما يكون :other بقيمة :value.',
    'different' => 'يجب أن يكون الحقلان :attribute و :other مُختلفين.',
    'digits' => 'يجب أن يحتوي حقل :attribute على :digits رقمًا/أرقام.',
    'digits_between' => 'يجب أن يحتوي حقل :attribute بين :min و :max رقمًا/أرقام.',
    'dimensions' => 'الحقل :attribute يحتوي على أبعاد صورة غير صالحة.',
    'distinct' => 'للحقل :attribute قيمة مُكرّرة.',
    'doesnt_start_with' => 'الحقل :attribute يجب ألّا يبدأ بأحد القيم التالية: :values.',
    'email' => 'يجب أن يكون حقل :attribute عنوان بريد إلكتروني صحيح البُنية.',
    'ends_with' => 'يجب أن ينتهي حقل :attribute بأحد القيم التالية: :values',
    'enum' => 'قيمة حقل :attribute غير موجودة في قائمة القيم المسموح بها.',
    'exists' => 'قيمة الحقل :attribute غير موجودة.',
    'file' => 'الحقل :attribute يجب أن يكون ملفا.',
    'filled' => 'حقل :attribute إجباري.',
    'gt' => [
        'array' => 'يجب أن يحتوي حقل :attribute على أكثر من :value عناصر/عنصر.',
        'file' => 'يجب أن يكون حجم ملف حقل :attribute أكبر من :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة حقل :attribute أكبر من :value.',
        'string' => 'يجب أن يكون طول نّص حقل :attribute أكثر من :value حروفٍ/حرفًا.',
    ],
    'gte' => [
        'array' => 'يجب أن يحتوي حقل :attribute على الأقل على :value عُنصرًا/عناصر.',
        'file' => 'يجب أن يكون حجم ملف حقل :attribute على الأقل :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة حقل :attribute مساوية أو أكبر من :value.',
        'string' => 'يجب أن يكون طول نّص حقل :attribute على الأقل :value حروفٍ/حرفًا.',
    ],
    'image' => 'يجب أن يكون حقل :attribute صورةً.',
    'in' => 'قيمة حقل :attribute غير صحيحة.',
    'in_array' => 'قيمة الحقل :attribute غير موجودة في :other.',
    'integer' => 'يجب أن تكون قيمة حقل :attribute عددًا صحيحًا.',
    'ip' => 'يجب أن تكون قيمة حقل :attribute عنوان IP صحيحًا.',
    'ipv4' => 'يجب أن تكون قيمة حقل :attribute عنوان IPv4 صحيحًا.',
    'ipv6' => 'يجب أن تكون قيمة حقل :attribute عنوان IPv6 صحيحًا.',
    'json' => 'يجب أن تكون قيمة حقل :attribute نصّا من نوع JSON.',
    'lt' => [
        'array' => 'يجب أن يحتوي حقل :attribute على أقل من :value عناصر/عنصر.',
        'file' => 'يجب أن يكون حجم ملف حقل :attribute أصغر من :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة حقل :attribute أصغر من :value.',
        'string' => 'يجب أن يكون طول نّص حقل :attribute أقل من :value حروفٍ/حرفًا.',
    ],
    'lte' => [
        'array' => 'يجب أن يحتوي حقل :attribute على :value عنصرا كحد أقصى.',
        'file' => 'يجب أن يكون حجم ملف حقل :attribute مساويا أو أصغر من :value كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة حقل :attribute مساوية أو أصغر من :value.',
        'string' => 'يجب أن يكون طول نّص حقل :attribute مساويا أو أقل من :value حروفٍ/حرفًا.',
    ],
    'max' => [
        'array' => 'يجب أن لا يحتوي حقل :attribute على أكثر من :max عناصر/عنصر.',
        'file' => 'يجب أن لا يتجاوز حجم ملف حقل :attribute :max كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة حقل :attribute مساوية أو أصغر من :max.',
        'string' => 'يجب أن لا يتجاوز طول نّص حقل :attribute :max حروفٍ/حرفًا.',
    ],
    'mimes' => 'يجب أن يكون ملفًا من نوع: :values.',
    'mimetypes' => 'يجب أن يكون ملفًا من نوع: :values.',
    'min' => [
        'array' => 'يجب أن يحتوي حقل :attribute على الأقل على :min عُنصرًا/عناصر.',
        'file' => 'يجب أن يكون حجم ملف حقل :attribute على الأقل :min كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة حقل :attribute مساوية أو أكبر من :min.',
        'string' => 'يجب أن يكون طول نّص حقل :attribute على الأقل :min حروفٍ/حرفًا.',
    ],
    'not_in' => 'قيمة حقل :attribute غير صحيحة.',
    'not_regex' => 'صيغة حقل :attribute غير صحيحة.',
    'numeric' => 'يجب أن تكون قيمة حقل :attribute رقمًا.',
    'password' => 'كلمة المرور غير صحيحة.',
    'present' => 'يجب تقديم حقل :attribute.',
    'regex' => 'صيغة حقل :attribute غير صحيحة.',
    'required' => 'حقل :attribute مطلوب.',
    'required_if' => 'حقل :attribute مطلوب في حال ما إذا كان :other يساوي :value.',
    'required_unless' => 'حقل :attribute مطلوب في حال ما لم يكن :other يساوي :values.',
    'required_with' => 'حقل :attribute مطلوب إذا توفّر :values.',
    'required_with_all' => 'حقل :attribute مطلوب إذا توفّر :values.',
    'required_without' => 'حقل :attribute مطلوب إذا لم يتوفّر :values.',
    'required_without_all' => 'حقل :attribute مطلوب إذا لم يتوفّر :values.',
    'same' => 'يجب أن يتطابق حقل :attribute مع :other.',
    'size' => [
        'array' => 'يجب أن يحتوي حقل :attribute على :size عنصرٍ/عناصر بالضبط.',
        'file' => 'يجب أن يكون حجم ملف حقل :attribute :size كيلوبايت.',
        'numeric' => 'يجب أن تكون قيمة حقل :attribute مساوية لـ :size.',
        'string' => 'يجب أن يحتوي نص حقل :attribute على :size حروفٍ/حرفًا بالضبط.',
    ],
    'string' => 'يجب أن يكون حقل :attribute نصآ.',
    'timezone' => 'يجب أن يكون حقل :attribute نطاقًا زمنيًا صحيحًا.',
    'unique' => 'قيمة حقل :attribute مُستخدمة من قبل.',
    'uploaded' => 'فشل في تحميل حقل :attribute.',
    'url' => 'صيغة الرابط حقل :attribute غير صحيحة.',
    'uuid' => 'يجب أن تكون قيمة حقل :attribute UUID صالحة.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'address' => 'العنوان',
        'age' => 'العمر',
        'amount' => 'المبلغ',
        'area' => 'المنطقة',
        'available' => 'متاح',
        'birthday' => 'تاريخ الميلاد',
        'body' => 'المحتوى',
        'city' => 'المدينة',
        'content' => 'المحتوى',
        'country' => 'الدولة',
        'created_at' => 'أُنشئ في',
        'creator' => 'المنشئ',
        'currency' => 'العملة',
        'current_password' => 'كلمة المرور الحالية',
        'customer' => 'العميل',
        'date' => 'التاريخ',
        'date_of_birth' => 'تاريخ الميلاد',
        'day' => 'اليوم',
        'deleted_at' => 'حُذف في',
        'description' => 'الوصف',
        'email' => 'البريد الإلكتروني',
        'excerpt' => 'المقتطف',
        'first_name' => 'الاسم الأول',
        'gender' => 'الجنس',
        'hour' => 'ساعة',
        'image' => 'صورة',
        'last_name' => 'اسم العائلة',
        'message' => 'الرسالة',
        'mobile' => 'الجوال',
        'month' => 'الشهر',
        'name' => 'الاسم',
        'password' => 'كلمة المرور',
        'password_confirmation' => 'تأكيد كلمة المرور',
        'phone' => 'الهاتف',
        'photo' => 'الصورة',
        'price' => 'السعر',
        'product_id' => 'معرف المنتج',
        'quantity' => 'الكمية',
        'subject' => 'الموضوع',
        'time' => 'الوقت',
        'title' => 'العنوان',
        'type' => 'النوع',
        'updated_at' => 'حُدث في',
        'username' => 'اسم المستخدم',
        'year' => 'السنة',
    ],
];
