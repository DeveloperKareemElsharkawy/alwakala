<?php

return [
    'general' => [
        'error' => 'حدث خطأ ما',
        'at_least_one' => 'يجب علي الاقل ادخال احد الحقول',
        'created' => 'تم  الانشاء',
        'listed' => 'تم عرض البيانات',
        'not_found' => 'غير موجود',
        'forbidden' => 'غير مسموح',
        'store_exists' => 'تحقق من المتجر',
        'success' => 'نجح',
        'size' => 'الحجم',

    ],
    'auth' => [
        'error' => 'حدث خطأ ما',
        'no_store' => 'ليس لديك متجر',
        'invalid_login_data' => 'خطأ فى كلمه المرور',
        'seller_not_found' => 'لم يتم العثور على هذا الحساب',
        'login' => 'تم تسجيل الدخول',
        'logout' => 'تم تسجيل الخروج',
        'store_created' => 'تم انشاء المتجر ستتم مراجعة البيانات',
        'register_step1' => 'تم انهاء الخطوة الاولي من التسجيل بنجاح',
        'device_token_updated' => 'تم تحديث رمز الجهاز',
        'notification_sent' => 'تم ارسال الاشعار',
        'reset_code_sent' => 'تم ارسال رمز التحقق',
        'invalid_code' => 'الرجاء إدخال الرمز الصحيح المرسل على رقمك',
        'valid_code' => 'رمز التحقق صحيح',
        'expired_code' => 'انتهت صلاحية الكود',
        'cannot_change_pass' => 'تأكد من صحة رمز التحقق',
        'pass_changed' => 'تم تغيير كلمة المرور',
        'email_changed' => 'تم تغيير الايميل ',
        'mobile_changed' => 'تم تغيير رقم الموبايل ',
        'side_data' => 'بيانات القائمة الجانبية',
        'type_error' => 'خطأ',
        'user_created' => 'تم انشاء المستخدم بنجاح',
        'access_deny' => 'ليس ليك صلاحية الدخول',
        'user_updated' => 'تم تعديل المستخدم بنجاح',
        'access_deleted' => 'تم مسح المستخدم بنجاح',
        'agreement' => 'F.A.Q Agreement',
        'wrong_password' => 'كلمة المرور غير صحيحه',
        'chose_diff_pass' => 'اختر كلمة مرور جدديدة',
        'access_denied' => 'ليس ليك صلاحية الدخول',
        'token_check' => 'تحقيق الرمز',
        'mobile_or_email_required' => 'رقم الموبايل أو البريد مطلوب',
        'email_exists_validation' => 'البريد غير مسجل ',
        'mobile_exists_validation' => 'رقم الهاتف غير موجود',
        'name_regex' => 'اسم البائع مدعوم فقط بالأحرف والمسافات والاقتباس الفردي والشرطة والنقطة',
        'store_name_regex' => 'اسم المتجر مدعوم فقط بالأحرف والمسافات والاقتباس الفردي والشرطة والنقطة',
        'legal_name_regex' => 'اسم القانونى مدعوم فقط بالأحرف والمسافات والاقتباس الفردي والشرطة والنقطة',
        'pending_for_review' => 'جاري مراجعة بياناتك الشخصية و بيانات المتجر و سيتم اشعارك لاحقا',
    ],

    'inventory' => [
        'not_active' => 'المنتج غير مفعل',
        'unauthorized' => 'أنت لا تملك هذا المنتج',
        'stock_updated' => 'تم تحديث المخزون',
        'discount_added' => 'تم اضافة الخصم',
        'has_bundle' => 'لابد ان يكون المنتج له سعر واحد لاضافة تخفيض',
        'delete_bundle_price' => 'حذف سعر الحزمة ',
        'bundle_price_deleted' => 'تم حذف سعر الخزمه',
        'bundle_price_not_found' => 'سعر الحزمه غير موجود',
        'image_deleted' => 'تم حذف الصوره',
        'product_minimum_image_limit' => 'يجب أن يحتوي المنتج على صورة واحدة على الأقل لكل لون',
        'product_inventory' => 'مخزون المنتج',
        'inventory_inserted_successfully' => 'تم إضافه المخزون بنجاح',
        'inventory_status' => 'حالة المخزون',
        'packing_units' => 'وحدات التعبئة',
        'owner_update_info_limit' => 'يمكن للمالك فقط تحديث المعلومات الرئيسية',
        'stock_updated' => 'تم تحديث المخزون',
        'update_image_limit' => 'يمكن للمالك فقط إضافة الصورة',

    ],

    'cart' => [
        'available' => 'متاح',
        'not_available' => 'غير متاح',
        'valid' => 'صالح',
        'invalid_cart' => 'لا يمكنك شراء منتجاتك',
        'product_not_found' => 'لم يتم العثور على المنتج',
        'shopping_cart_added' => 'تم إضافه عربه تسوق',
        'product_color_stock_empty' => 'المنتج ليس به مخزون',
        'you_own_this_product' => 'أنت تملك هذا المنتج',
        'invalid_quantity' => 'الكميه غير صحيحه',
        'quantity_updated' => 'تم تغيير الكمية',
        'cart_deleted' => 'تم حذف المنتج',
        'apply' => 'الكوبون غير متاح للفتره الحاليه',
        'quantity' => 'الكمية المتاحة من الكوبون هي 0',
        'purchase_amount' => 'إجمالي سله الشراء أقل من الإجمالي المتاح به لاستخدام هذا الكوبون',
        'coupon_not_found' => 'هذا الكوبون غير متاح حاليا',
        'coupon_inactive' => 'هذا الكوبون غير متاح حاليا'
    ],
    'coupon' => [
        'quantity' => 'الكمية المتاحة من الكوبون هي 0',
        'purchase_amount' => 'إجمالي سله الشراء أقل من الإجمالي المتاح به لاستخدام هذا الكوبون',
        'not_found' => 'هذا الكوبون غير موجود',
        'qty_is_over' => 'تم إستخدام الحد الأقسى لهذا الكوبون',
        'inactive' => 'هذا الكوبون غير متاح حاليا',
        'ended' => 'الكوبون غير متاح للفتره الحاليه',
        'no_products_for_this_coupon' => 'لا يوجد منتجات لهذا الكوبون',
    ],

    'status' => [
        'available' => 'متاح',
        'not_available' => 'غير متاح',
        'in_review' => 'قيد المراجعه',
        'soon' => 'قريبا'
    ],

    'order' => [
        'no_cart_available' => 'لا يوجد عربة متاحة',
        'no_quantities_available' => 'الكميات غير متوفرة',
        'cannot_cancel' => 'لا يمكن إلغاء الطلب',
        'canceled' => 'تم إلفاء الطلب بنجاح',
        'already_canceled' => 'تم إلفاء الطلب بالفعل',
        'add_order_id' => 'أدخل الرقم المعرف للطلب من فضلك',
        'cannot_receive_in_progress_order' => 'لا يمكن تلقي أوامر غير قيد التقدم',
        'consumer_unit_publish_date' => 'الرجاء إدخال سعر المستهلك وتاريخ النشر لهذه المنتجات',
        'received' => 'تم استلام الطلب بنجاح',
        'available' => 'متاح',
        'retrieved_all' => 'استرجاع جميع الطلبات',
        'retrieved' => 'تم الاسترجاع',
        'not_found' => 'لم يتم العثور على الطلب',
        'only_approve_reject' => 'يمكنك فقط الموافقة على هذا الأمر أو رفضه',
        'only_approve_reject_yours' => 'يمكنك فقط الموافقة على طلباتك أو رفضها',
        'no_products_to_reject' => 'لا يوجد منتجات لرفضها',
        'no_products_to_approve' => 'لا يوجد منتجات لقبولها',
        'is_issued' => 'لا يمكنك الموافقة أو رفض الطلب برجاء مراجعه الحاله',
        'rejected' => 'تم رفض الطلب',
        'approved' => 'تم الموافقه على الطلب بنجاح',
        'today_orders' => ' طلبات اليوم',
        'statuses' => 'حالات الطلب',
        'add' => 'تم اضافة الطلب',
        'payment_methods' => 'طرق الدفع',
        'shipping' => 'جاري شحن الطلب ',
        'order_product_false_ownership' => 'لا تملك هذا المنتج',
        'order_products_status_not_valid' => 'حالات المنتجات المرسله رقم :products لم يتم شحنها بعد',
        'product' => [
            'rejected' => 'تم رفض منتج الطلب',
        ]
    ],

    'addresses' => [
        'retrieved_all' => 'العناوين',
        'retrieved' => 'العنوان',
        'added' => 'تمت اضافة العنوان',
        'edited' => 'تم تحديث العنوان',
        'deleted' => 'تم حذف العنوان',
        'set_default' => 'تم تغير عنوان الاستلام',
        'wrong_address' => 'عنوان خاطئ',
    ],

    'stores' => [
        'image_added' => 'تمت اضافة الصورة',
        'image_deleted' => 'تمت ازالة الصورة',
        'many_images' => 'غير مسموح باكثر من 5 صور',
        'call_added' => 'تمت اضافة المكالمة',
        'image_not_found' => 'الصورة غير موجودة',
        'follow_store' => 'تمت المتابعة بنجاح',
        'un_follow_store' => 'تمت الغاء المتابعة بنجاح',
        'follow_denied' => 'لا يمكنك المتابعة',
        'favorite_denied' => 'لا يمكنك تفضيل منتجك إختر منتج أخر',
        'show_to_consumer' => 'تم عرض المنتج للمستهلك',
        'hide_to_consumer' => 'تم اخفاء المنتج للمستهلك',
        'store_list' => 'المتاجر',
        'store_cover_area' => 'المناطق التى يغطيها هذا التاجر',
        'store_area_not_found' => 'المنطقه غير موجوده',
        'store_profile' => '  صفحة المتجر ',
        'store_status' => '  صفحة التفعيل ',
        'store_not_found' => 'لم يتم العثور على المتجر',
        'profile_updated' => 'تم تحديث الملف الشخصي ',
        'link_updated' => ' تم تحديث الرابط  ',
        'store_home' => 'صفحة المتجر الرئيسية',
        'store_products' => 'منتجات المتجر',
        'store_feeds' => 'حفظ أحر الأخبار',
        'store_rates' => 'تقييمات المتجر',
        'store_cover_added' => 'تم إضافة صوره الحلفيه للمتجر',
        'store_rate_added' => 'تم إضافة تقييم للمتجر',
        'store_logs' => 'سجلات المتجر',
        'store_hot_offers' => 'أفضل عروض المتجر',
        'store_best_selling' => 'أفضل مبيعات المتجر',
        'store_new_arrival' => 'وصل حديثا للمتجر',
        'store_category_products' => 'منتجات من أقسام المتجر',
        'near_by_stores' => 'بالقرب من المتاجر',
        'search_products' => 'بحث عن المنتجات',
        'search_stores' => 'البحث عن المتاجر',
        'open_hours_added' => 'تمت إضافة ساعات العمل المفتوحة',
        'open_hours_not_found' => 'لم يتم إضافه ساعات العمل من قبل هذا التاجر',
        'week_days' => 'أيام الأسبوع',
        'store_favorite' => 'تم اضافه المحل للمفضله',
        'store_unfavorite' => 'تم حذف المتجر من قائمه المفضلة',
        'store_types_retrieved' => 'تم استرداد أنواع المتاجر بنجاح ',
        'category_store' => 'قسم المتجر',
        'colors_retrieved' => 'تم استرداد الألوان بنجاح ',
        'updated' => 'تم تحديث معلومات المتجر',
        'stores_count' => 'عدد المتاجر المتابعه',
        'upload_documents' => 'تم رفع الملفات ',
        'not_found' => 'لم يتم العثور على المتجر'
    ],

    'shipping' => [
        'shipping_company' => 'شركات الشحن',
        'shipping_method' => 'طريقه الشحن',
        'shipping_company_created' => 'تم انشاء شركة الشحن',
        'company_noy_exists' => 'شركة الشحن غير موجودة',
        'company_deleted' => 'تم حذف شركة الشحن',
        'company_location' => 'مقرات شركة الشحن',
        'company_lines' => 'خطوط شركة الشحن',
        'company_rated' => 'تم التقييم',
    ],

    'reports' => [
        'report_added' => 'Report sent'
    ],
    'category' => [
        'un_valid_parent' => 'رقم التصنيف غير صالح',
        'un_valid_sub_category' => 'رقم التصنيف غير صالح',
        'un_required_packing_unit' => 'رقم وحدة التعبئة غير مطلوب',
        'id_required' => 'category_id مطلوب',
        'delete_error' => 'لا يمكن حذف القسم لاإرتباطه بجدول  :model'
    ],
    'actions' => [
        'create_productS1' => 'اضافة الخطوة الاولى من المنتج',
        'create_productS2' => 'اضافة الخطوة الثانية من المنتج',
        'product_added' => 'تم إضافه المنتج',
        'create_store' => 'تم اضافة المتجر',
        'add_discount' => 'تم اضافة الخصم',
        'increase_stock' => 'تم تحديث المخزن',
        'inventory_created' => 'تم اضافة المخزون',
        'update_store' => 'تم تعديل المتجر',
        'rate_added' => 'تم التقييم',
        'store_cover_added' => 'تم اضافة الغلاف',
        'store_categories_updated' => 'تم تعديل التصنيف',
        'store_feed_link_updated' => 'تم اضافة رابط التغدية',
        'store_area_added' => 'تم إضافه منطقه جديده',
        'store_area_updated' => 'تم تعديل المنطقه',
        'store_area_deleted' => 'تم حذف المنطقه',
        'un_valid_parent' => 'رقم القسم غير صالح',
        'un_required_packing_unit' => ' مطلوب معرف وحدة التعبئة',
        'id_required' => 'رقم القسم مطلوب',
        'favorite_products' => 'المنتجات المفضلة',

    ],
    'system' => [
        'created' => 'تم الاضافه بنجاح',
        'updated' => 'تم التعديل بنجاح',
        'deleted' => 'تم الحذف بنجاح',
    ],
    'product' => [
        'not_owner' => 'لا يمكنك التعديل لست صاحب المنتح',
        'active' => 'تم التفعيل بنجاح',
        'not_active' => 'تم التعطيل بنجاح',
        'not_valid' => 'لا يمكنك اكمال العملية',
        'reviewed' => 'تم المراجعة بنجاح',
        'cant_reviewed' => 'لا يمكنك مراجعة المنتج',
        'barcode_used' => 'الباركود مستخدم',
        'created' => 'تم انشاء المنتج',
        'sumOfMaterial' => 'نسبة الخامات يجب ان لا تزيد عن 100',
        'unfavourite' => 'تم حذف المنتج من المفضلة',
        'favorite' => 'تم اضافة المنتج الى المفضلة',
        'product_under_review' => 'المنتج تحت المراجعة',
        'product_not_found' => 'لم يتم العثور على المنتج',
        'products' => 'المنتجات',
        'review_added' => 'تم إضافة التقييم بنجاح',
        'updated' => 'تم تحديث معلومات المنتج الرئيسية',
        'price_updated' => 'تم تحديث السعر',
        'product_stock_owner_update_limit' => 'يمكن للمالك فقط تحديث المخزون',
        'wrong_barcode' => 'رقم الباركود خاطئ',
        'fav_products_count' => 'عدد المنتجات المفضله',
        'no_product' => 'برجاء مراجعة ال product id',
        'no_store' => 'برجاء مراجعه المتجر الخاص بهذا المنتج',
        'seller_type_not_supplier' => 'يجب أن تكون تاجر لكى تضيف هذا المنتج',

    ],
    'errors' => [
        '500' => 'خطأ في الاتصال ',
    ],

    'materials' => [
        'destroy' => 'تم الحذف بنجاح',
        'update' => 'تم التعديل بنجاح ',
        'save' => 'تم الحفظ بنجاح ',
        'can_not_destroy' => 'لا يمكنك المسح توجد منتجات تحت هذه المادة'
    ],

    'colors' => [
        'exists' => 'اللون مستخدم من قبل',
    ],
    'notifications' => [
        'review_product' => 'تمت مراجعة المنتج الخاص بك',
        'favorite_store' => 'تم اضافة المخزن الخاص بك الى المفضلة ',
        'follow_store' => 'تم متابعة المخزن الخاص بك ',
        'approve_order' => 'تم قبول طلبك',
        'reject_order' => 'تم رفض طلبك ',
        'add_order' => 'تمت اضافة طلبك ',
        'receive_order' => 'تم استلام طلبك ',
        'favorite_product' => 'تم اضافة المنتج الى المفضلة',
        'make_read' => 'تم قراءة الاشعار بنجاح',
        'approve_seller' => 'تم تفعيل حسابك الخاص',
    ],
    'roles' => [
        'purchase_manger' => 'مدير مشتريات',
        'sales_manger' => 'مدير مبيعات',
        'sales' => 'مبيعات',
        'cannot_creat_owner' => 'لايمكن اضافه صاحب متجر اخر',
        'list_roles' => 'تم عرض الادوار',
    ],

    'sections' => [
        'home' => 'الرئيسية',
        'just_for_you' => 'مخصص من أجلك',
        'feeds_list' => 'قائمه أخر الأخبار',
        'sections' => 'الأفسام',
        'brands' => 'العلامات التجارية',
        'new_arrivals' => 'وصل حديثا',
        'most_popular' => 'الأكثر شعبية',
        'inventory' => 'المخزون',
        'countries' => 'الدول',
        'states' => 'المحافظات',
        'cities' => 'المدن',
        'sizes' => 'المقاسات',

    ],
    'feeds' => [

        'upload_video' => 'تم رفع الفديو',
        'delete_video' => 'تم مسح الفديو',
        'favorite' => 'تم إضافه الفييد الى المفضله',
        'unfavorite' => 'تم حذف الفييد من المفضله',
    ],

    'barcode' => [
        'update_limit' => 'يمكن للمالك فقط تحديث الباركود ',
        'updated' => 'تم ىحديث الباركود'
    ],
    'package' => [
        'update_limit' => 'يمكن للمالك فقط تحديث المجموعه ',
        'updated' => 'تم ىحديث المجموعه',
        'update_bundles_limit' => 'يمكنك فقط إضافة ثلاث حزم',
        'bundle_added' => 'تمت إضافة الحزمة'
    ],
    'measurements' => [
        'save' => 'تم حفظ القياس بنجاح',
        'already_exist' => 'القياس موجود بالفعل يرجى إختيار قسم أو قياس أخر',
        'update' => 'تم مسح القياس بنجاح',
        'not_found' => 'القياس غير موجود',
        'deleted' => 'تم تعديل القياس بنجاح',

    ],
    'policy' => [
        'get_policies' => 'جميع السياسات',
    ],

    'payment_methods' => [
        'created' => 'تم إضافه طريقه الدفع',
        'updated' => 'تم تعديل طريقه الدفع',
        'deleted' => 'تم حذف طريقه الدفع',
    ],
    'sms' => [
        'store_register' => 'تم تسجيل حساب الوكاله الخاص بك بنجاح. كود التفعيل هو : ' . ':code ',
        'store_forget_password' => 'كود اعاده كلمه المرور الخاص بك في حساب الوكاله : ' . ':code ',
        'store_change_credential_mobile' => 'كود تغير رقم الموبايل الخاص بك في حساب الوكاله : ' . ':code ',
        'store_change_credential_email' => 'كود تغير الايميل الخاص بك في حساب الوكاله : ' . ':code ',
    ],
    'notifications' => [
        'store_account_confirmed' => 'تم تاكيد حساب الوكاله الخاص بك بنجاح.',
    ],
    'reviews' => [
        'add' => 'تمت اضافة التققيم بنجاح ',
        'delete' => 'تم مسح التقييم بنجاج',
        'forbidden' => 'لايمكنك اضافة التقيييم ',
        'in_valid' => 'قيمة الحاله غير صالحه',
        'status_changed' => 'تم تغيير الحاله بنجاح '
    ],
    'packages' => ['add' => 'طلبك قيد التنفيذ', 'forbidden' => 'لا يمكنك الاشتراك'],
    'warehouse' => [
        'not_found' => 'برجاء مراجعة المخزن أولا',
        'not_owner' => 'لا يمكنك استخدام هذا المخزن '
    ],
    'feed' => [
        'created' => 'تم إنشاء فييد جديده',
        'updated' => 'تم تعديل بيانات الفييد',
        'deleted' => 'تم حذف الفييد',
        'unauthorized' => 'لا تملك الفييد المحدده',
        'validation' => [
            'youtube_url_error' => 'رابط اليوتيوب غير صالح',
            'only_images_or_youtube_url' => 'برجاء إختيار لينك يوتيوب أو صور فقد',
        ]
    ],

    'offers' => [
        'invalid_product' => "المنتج غير موجود أو خارج سياسة الوكالة",
        'accept_your_offer_err' => "لا يمكن إتخاذ إجراء على العرض الخاص بك",
        'accept_not_your_offer_err' => "لا يمكنك الإشتراك فى هذا العرض",
        'already_submitted_this_offer' => "لقد وافقت أو رفضت هذا العرض بالفعل",
        'offer_approved_successfully' => "تم قبول العرض بنجاح",
        'offer_rejected_successfully' => "تم رفض العرض بنجاح",
        'offer_ownership_err' => "لا تملك هذا العرض",
        'offer_closed' => "تم إنهاء هذا العرض بنجاح",
        'offer_already_closed' => "هذا العرض مغلق بالفعل",
        'offer_already_approved' => "أنت بالفعل مشترك فى هذا العرض",
        'offer_product_already_exist' => "المنتج :name موجود بالفعل فى عروض مفعله",
    ],
];
