<?php

namespace Database\Seeders;

use App\Enums\Roles\ARoles;
use App\Enums\SizeTypes\ASizeTypes;
use App\Enums\UserTypes\UserRole;
use App\Enums\UserTypes\UserType;
use App\Models\Admin;
use App\Models\App;
use App\Models\AppTvType;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\DaysOfWeek;
use App\Models\HomeSection;
use App\Models\Material;
use App\Models\OrderStatus;
use App\Models\PackingUnit;
use App\Models\Role;
use App\Models\Size;
use App\Models\SizeType;
use App\Models\StoreType;
use App\Models\User;
use Illuminate\Database\Seeder;
use DB;


class LookUpsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        $this->createApps();
//        $this->createHomeSections();
//        $this->creatStoreTypes();
//        $this->creatTransactionTypes();
//        $this->creatSizeTypes();
//        $this->creatColors();
//        $this->creatOrderStatus();
//        $this->creatPackingUnits();
//        $this->createCategories();
//        $this->creatDaysOfWeeks();
//        $this->createMaterials();
//        $this->creatSizes();

        $this->createAdmins();
//        $this->createBrands();
    }

    /**
     * create Admins
     */
    protected function createAdmins()
    {
        $password = \Illuminate\Support\Facades\Hash::make('12345678');
        $adminRoleId = Role::query()->where('role', ARoles::SUPER_USER)->first()->id;

        $ibrahim = User::query()->updateOrCreate(
            ['name' => 'ibrahim', 'email' => 'developer.essam@elwekala.com', 'mobile' => '01000709271', 'password' => $password, 'type_id' => UserType::ADMIN, 'activation' => true],
            ['email' => 'developer.essam@elwekala.com']
        );
        Admin::query()->updateOrCreate(
            ['user_id' => $ibrahim->id, 'role_id' => $adminRoleId],
            ['user_id' => $ibrahim->id, 'role_id' => $adminRoleId]
        );
        $abdelrahman = User::query()->updateOrCreate(
            ['name' => 'abdelrahman', 'email' => 'abdelrahman@elwekala.com', 'mobile' => '01008863738', 'password' => $password, 'type_id' => UserType::ADMIN, 'activation' => true],
            ['email' => 'abdelrahman@elwekala.com']
        );
        Admin::query()->updateOrCreate(
            ['user_id' => $abdelrahman->id, 'role_id' => $adminRoleId],
            ['user_id' => $abdelrahman->id, 'role_id' => $adminRoleId]
        );

        $badr = User::query()->updateOrCreate(
            ['name' => 'badr', 'email' => 'badr@elwekala.com', 'mobile' => '01004042402', 'password' => $password, 'type_id' => UserType::ADMIN, 'activation' => true],
            ['email' => 'badr@elwekala.com']
        );
        Admin::query()->updateOrCreate(
            ['user_id' => $badr->id, 'role_id' => $adminRoleId],
            ['user_id' => $badr->id, 'role_id' => $adminRoleId]
        );

        $essam = User::query()->updateOrCreate(
            ['name' => 'essam', 'email' => 'essam@elwekala.com', 'mobile' => '01000709271', 'password' => $password, 'type_id' => UserType::ADMIN, 'activation' => true],
            ['email' => 'essam@elwekala.com']
        );
        Admin::query()->updateOrCreate(
            ['user_id' => $essam->id, 'role_id' => $adminRoleId],
            ['user_id' => $essam->id, 'role_id' => $adminRoleId]
        );

        $hasnaa = User::query()->updateOrCreate(
            ['name' => 'hasnaa', 'email' => 'hasnaa@elwekala.com', 'mobile' => '01000000000', 'password' => $password, 'type_id' => UserType::ADMIN, 'activation' => true],
            ['email' => 'ehasnaa@elwekala.com']
        );
        Admin::query()->updateOrCreate(
            ['user_id' => $hasnaa->id, 'role_id' => $adminRoleId],
            ['user_id' => $hasnaa->id, 'role_id' => $adminRoleId]
        );
        //
        $sarah = User::query()->updateOrCreate(
            ['name' => 'sarah', 'email' => 'sarah@elwekala.com', 'mobile' => '01000000001', 'password' => $password, 'type_id' => UserType::ADMIN, 'activation' => true],
            ['email' => 'sarah@elwekala.com']
        );
        Admin::query()->updateOrCreate(
            ['user_id' => $sarah->id, 'role_id' => $adminRoleId],
            ['user_id' => $sarah->id, 'role_id' => $adminRoleId]
        );
        //
        $kareem = User::query()->updateOrCreate(
            ['name' => 'kareem', 'email' => 'kareem@elwekala.com', 'mobile' => '01097605373', 'password' => $password, 'type_id' => UserType::ADMIN, 'activation' => true],
            ['email' => 'kareem@elwekala.com']
        );
        Admin::query()->updateOrCreate(
            ['user_id' => $kareem->id, 'role_id' => $adminRoleId],
            ['user_id' => $kareem->id, 'role_id' => $adminRoleId]
        );
    }

    /**
     * create Apps
     */
    protected function createApps()
    {
        App::query()->updateOrCreate(
            ['app_en' => 'seller-app', 'app_ar' => 'تطبيق التاجر'],
            ['app_en' => 'seller-app', 'app_ar' => 'تطبيق التاجر']
        );
        App::query()->updateOrCreate(
            ['app_en' => 'consumer-app', 'app_ar' => 'تطبيق المستهلك'],
            ['app_en' => 'consumer-app', 'app_ar' => 'تطبيق المستهلك']
        );
    }


    /**
     * create create Home Sections
     */
    protected function createHomeSections()
    {
        HomeSection::query()->updateOrCreate(
            [
                'name_ar' => 'وصل حديثا',
                'name_en' => 'New Arrival',
                 'activation' => true,
                 'image' => '/images/stores/images/159896528833.jpg'
            ],
            ['name_en' => 'New Arrival', 'name_ar' => 'وصل حديثا']
        );

        HomeSection::query()->updateOrCreate(
            [
                'name_ar' => 'الأكثر شهرة',
                'name_en' => 'Most Popular',
                 'activation' => true,
                 'image' => '/images/stores/images/159896528833.jpg'
            ],
            ['name_en' => 'Most Popular', 'name_ar' => 'الأكثر شهرة']
        );

        HomeSection::query()->updateOrCreate(
            [
                'name_ar' => 'المتاجر المقترحة',
                'name_en' => 'Stores For You',
                 'activation' => true,
                 'image' => '/images/stores/images/159896528833.jpg'
            ],
            ['name_en' => 'Stores For You', 'name_ar' => 'المتاجر المقترحة']
        );


        HomeSection::query()->updateOrCreate(
            [
                'name_ar' => 'أخر الأخبار',
                'name_en' => 'Feeds',
                 'activation' => true,
                 'image' => '/images/stores/images/159896528833.jpg'
            ],
            ['name_en' => 'Feeds', 'name_ar' => 'أخر الأخبار']
        );
    }

    /**
     * create Apps Tv Types
     */
    protected function createAppsTvTypes()
    {
        AppTvType::query()->updateOrCreate(
            ['type_en' => 'PRODUCT', 'type_ar' => 'منتج'],
            ['type_en' => 'PRODUCT', 'type_ar' => 'منتج']
        );
        AppTvType::query()->updateOrCreate(
            ['type_en' => 'STORE', 'type_ar' => 'متجر'],
            ['type_en' => 'STORE', 'type_ar' => 'متجر']
        );
        AppTvType::query()->updateOrCreate(
            ['type_en' => 'PAGE', 'type_ar' => 'صفحة'],
            ['type_en' => 'PAGE', 'type_ar' => 'صفحة']
        );
    }


    /**
     * create Materials
     */
    protected function createMaterials()
    {
        Material::query()->updateOrCreate(
            ['name_ar' => 'قطن', 'name_en' => 'Cotton'],
            ['name_ar' => 'قطن', 'name_en' => 'Cotton']
        );
        Material::query()->updateOrCreate(
            ['name_ar' => 'كتان', 'name_en' => 'Linen'],
            ['name_ar' => 'كتان', 'name_en' => 'Linen']
        );
        Material::query()->updateOrCreate(
            ['name_ar' => 'جبردين', 'name_en' => 'Gabardine'],
            ['name_ar' => 'جبردين', 'name_en' => 'Gabardine']
        );
        Material::query()->updateOrCreate(
            ['name_ar' => 'بوليستر', 'name_en' => 'Polyester'],
            ['name_ar' => 'بوليستر', 'name_en' => 'Polyester']
        );
    }

    /**
     * create Categories Data
     */
    protected function createCategories()
    {
        //////////////////////////////////////////  Start of Main Category Men Fashion //////////////////////////////////////////
        $menFashionCategory = Category::query()->updateOrCreate(
            ['name_ar' => 'ملابس رجالى', 'name_en' => 'Men\'s Fashion', "description" => "description", "activation" => true],
            ['name_ar' => 'ملابس رجالى', 'name_en' => 'Men\'s Fashion']
        );
        /**
         * level2 pf mens fashion
         */
        $menClosing = Category::query()->updateOrCreate( // sub Category
            ['name_ar' => 'ملابس الرجال', 'name_en' => 'Men\'s Clothing', "description" => "description", "activation" => true, "category_id" => $menFashionCategory->id],
            ['name_ar' => 'ملابس الرجال', 'name_en' => 'Men\'s Clothing']
        );

        $underWear = Category::query()->updateOrCreate( // sub Category
            ['name_ar' => 'ثياب داخلية', 'name_en' => 'Underwear', "description" => "description", "activation" => true, "category_id" => $menFashionCategory->id],
            ['name_ar' => 'ثياب داخلية', 'name_en' => 'Underwear']
        );

        /**
         * level3 pf mens fashion
         */
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'تيشيرت وقميص', 'name_en' => 'T-Shirts & Shirts', "description" => "description",
                "activation" => true, "category_id" => $menClosing->id, 'packing_unit_id' => 2
            ],
            ['name_ar' => 'تيشيرت وقميص', 'name_en' => 'T-Shirts & Shirts']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'بنطال وجينز', 'name_en' => 'Pants & Jeans', "description" => "description",
                "activation" => true, "category_id" => $menClosing->id, 'packing_unit_id' => 6
            ],
            ['name_ar' => 'بنطال وجينز', 'name_en' => 'Pants & Jeans']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'جاكت ومعطف', 'name_en' => 'Jackets & Coats', "description" => "description",
                "activation" => true, "category_id" => $menClosing->id, 'packing_unit_id' => 4
            ],
            ['name_ar' => 'جاكت ومعطف', 'name_en' => 'Jackets & Coats']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'بدل', 'name_en' => 'Suits', "description" => "description",
                "activation" => true, "category_id" => $menClosing->id, 'packing_unit_id' => 4
            ],
            ['name_ar' => 'بدل', 'name_en' => 'Suits']
        );

        Category::query()->updateOrCreate(
            [
                'name_ar' => 'شورت داخلي', 'name_en' => 'Boxers', "description" => "description",
                "activation" => true, "category_id" => $underWear->id, 'packing_unit_id' => 7
            ],
            ['name_ar' => 'شورت داخلي', 'name_en' => 'Boxers']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'Trunks', 'name_en' => 'Trunks', "description" => "description",
                "activation" => true, "category_id" => $underWear->id, 'packing_unit_id' => 7
            ],
            ['name_ar' => 'Trunks', 'name_en' => 'Trunks']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'Lounge wear', 'name_en' => 'Lounge wear', "description" => "description",
                "activation" => true, "category_id" => $underWear->id, 'packing_unit_id' => 7
            ],
            ['name_ar' => 'Lounge wear', 'name_en' => 'Lounge wear']
        );

        //        Category::query()->updateOrCreate(
        //            ['name_ar' => 'صنادل', 'name_en' => 'Sandals', "description" => "description", "activation" => true, "category_id" => $menShoes->id],
        //            ['name_ar' => 'صنادل', 'name_en' => 'Sandals']
        //        );
        //        Category::query()->updateOrCreate(
        //            ['name_ar' => 'أحذية رياضية', 'name_en' => 'Sneakers', "description" => "description", "activation" => true, "category_id" => $menShoes->id],
        //            ['name_ar' => 'أحذية رياضية', 'name_en' => 'Sneakers']
        //        );
        //        Category::query()->updateOrCreate(
        //            ['name_ar' => 'كلاسيك', 'name_en' => 'Formal Shoes', "description" => "description", "activation" => true, "category_id" => $menShoes->id],
        //            ['name_ar' => 'كلاسيك', 'name_en' => 'Formal Shoes']
        //        );
        //////////////////////////////////////////  End of Main Category Men Fashion //////////////////////////////////////////


        //////////////////////////////////////////  Start of Main Category Men Fashion //////////////////////////////////////////
        $womenFashionCategory = Category::query()->updateOrCreate(
            ['name_ar' => 'ملابس حريمى', 'name_en' => 'Women\'s Fashion', "description" => "description", "activation" => true],
            ['name_ar' => 'ملابس حريمى', 'name_en' => 'Women\'s Fashion']
        );

        /**
         * level2 pf women fashion
         */
        $womenClothingCategory = Category::query()->updateOrCreate(
            ['name_ar' => 'ملابس النساء', 'name_en' => 'Women Clothing', "description" => "description", "activation" => true, "category_id" => $womenFashionCategory->id],
            ['name_ar' => 'ملابس النساء', 'name_en' => 'Women Clothing']
        );
        $lingeriesCategory = Category::query()->updateOrCreate(
            ['name_ar' => 'الملابس الداخلية', 'name_en' => 'Lingerie', "description" => "description", "activation" => true, "category_id" => $womenFashionCategory->id],
            ['name_ar' => 'الملابس الداخلية', 'name_en' => 'Lingerie']
        );

        /**
         * level3 of women fashion
         */

        Category::query()->updateOrCreate(
            [
                'name_ar' => 'فستان', 'name_en' => 'Dresses', "description" => "description",
                "activation" => true, "category_id" => $womenClothingCategory->id, 'packing_unit_id' => 2
            ],
            ['name_ar' => 'فستان', 'name_en' => 'Dresses']
        );

        Category::query()->updateOrCreate(
            [
                'name_ar' => 'الجزء العلوي', 'name_en' => 'Tops', "description" => "description",
                "activation" => true, "category_id" => $womenClothingCategory->id, 'packing_unit_id' => 3
            ],
            ['name_ar' => 'الجزء العلوي', 'name_en' => 'Tops']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'تنانير', 'name_en' => 'Skirts', "description" => "description",
                "activation" => true, "category_id" => $womenClothingCategory->id, 'packing_unit_id' => 4
            ],
            ['name_ar' => 'تنانير', 'name_en' => 'Skirts']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'جينز', 'name_en' => 'Jeans', "description" => "description",
                "activation" => true, "category_id" => $womenClothingCategory->id, 'packing_unit_id' => 6
            ],
            ['name_ar' => 'جينز', 'name_en' => 'Jeans']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'معاطف', 'name_en' => 'Coats', "description" => "description",
                "activation" => true, "category_id" => $womenClothingCategory->id, 'packing_unit_id' => 4
            ],
            ['name_ar' => 'معاطف', 'name_en' => 'Coats']
        );

        Category::query()->updateOrCreate(
            [
                'name_ar' => 'قمصان وبنطلونات', 'name_en' => 'Camisoles & Slips', "description" => "description",
                "activation" => true, "category_id" => $lingeriesCategory->id, 'packing_unit_id' => 2
            ],
            ['name_ar' => 'قمصان وبنطلونات', 'name_en' => 'Camisoles & Slips']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'سراويل', 'name_en' => 'Panties', "description" => "description",
                "activation" => true, "category_id" => $lingeriesCategory->id, 'packing_unit_id' => 6
            ],
            ['name_ar' => 'سراويل', 'name_en' => 'Panties']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'حمالات الصدر', 'name_en' => 'Bras', "description" => "description",
                "activation" => true, "category_id" => $lingeriesCategory->id, 'packing_unit_id' => 4
            ],
            ['name_ar' => 'حمالات الصدر', 'name_en' => 'Bras']
        );
        Category::query()->updateOrCreate(
            ['name_ar' => 'ملابس النوم', 'name_en' => 'Sleep & Loungewear', "description" =>
            "description", "activation" => true, "category_id" => $lingeriesCategory->id, 'packing_unit_id' => 4],
            ['name_ar' => 'ملابس النوم', 'name_en' => 'Sleep & Loungewear']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'الجلباب', 'name_en' => 'Robes', "description" => "description",
                "activation" => true, "category_id" => $lingeriesCategory->id, 'packing_unit_id' => 6
            ],
            ['name_ar' => 'الجلباب', 'name_en' => 'Robes']
        );
        //////////////////////////////////////////  End of Main Category Women Fashion //////////////////////////////////////////


        //////////////////////////////////////////  Start of Main Category Kids Fashion //////////////////////////////////////////
        $kidsFashionCategory = Category::query()->updateOrCreate(
            ['name_ar' => 'ملابس أطفال', 'name_en' => 'Kid\'s Fashion', "description" => "description", "activation" => true],
            ['name_ar' => 'ملابس أطفال', 'name_en' => 'Kid\'s Fashion']
        );

        /**
         * level2 of Kid's  Fashion
         */
        $boysShoes = Category::query()->updateOrCreate(
            ['name_ar' => 'أحذية أولاد', 'name_en' => 'Boys Shoes', "description" => "description", "activation" => true, "category_id" => $kidsFashionCategory->id],
            ['name_ar' => 'أحذية أولاد', 'name_en' => 'Boys Shoes']
        );

        /**
         * level3 of Boys  Shoes
         */
        Category::query()->updateOrCreate(
            ['name_ar' => 'أحذية رياضية', 'name_en' => 'Sneakers', "description" => "description", "activation" => true, "category_id" => $boysShoes->id],
            ['name_ar' => 'أحذية رياضية', 'name_en' => 'Sneakers']
        );

        Category::query()->updateOrCreate(
            [
                'name_ar' => 'أحذية رياضية وخارجية', 'name_en' => 'Athletic & Outdoor Shoes',
                "description" => "description", "activation" => true, "category_id" => $boysShoes->id, 'packing_unit_id' => 8
            ],
            ['name_ar' => 'أحذية رياضية وخارجية', 'name_en' => 'Athletic & Outdoor Shoes']
        );

        Category::query()->updateOrCreate(
            [
                'name_ar' => 'جزمات', 'name_en' => 'Boots', "description" => "description", "activation" => true,
                "category_id" => $boysShoes->id, 'packing_unit_id' => 8
            ],
            ['name_ar' => 'جزمات', 'name_en' => 'Boots']
        );

        Category::query()->updateOrCreate(
            [
                'name_ar' => 'شباشب', 'name_en' => 'Slippers', "description" => "description", "activation" => true,
                "category_id" => $boysShoes->id, 'packing_unit_id' => 8
            ],
            ['name_ar' => 'شباشب', 'name_en' => 'Slippers']
        );

        Category::query()->updateOrCreate(
            [
                'name_ar' => 'صنادل الموضة', 'name_en' => 'Fashion Sandals', "description" => "description",
                "activation" => true, "category_id" => $boysShoes->id, 'packing_unit_id' => 8
            ],
            ['name_ar' => 'صنادل الموضة', 'name_en' => 'Fashion Sandals']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'قباقيب', 'name_en' => 'Clogs', "description" => "description",
                "activation" => true, "category_id" => $boysShoes->id, 'packing_unit_id' => 8
            ],
            ['name_ar' => 'قباقيب', 'name_en' => 'Clogs']
        );

        /**
         * level2 of Kid's  Fashion
         */
        $girlsShoes = Category::query()->updateOrCreate(
            ['name_ar' => 'أحذية بنات', 'name_en' => 'Girls Shoes', "description" => "description", "activation" => true, "category_id" => $kidsFashionCategory->id],
            ['name_ar' => 'أحذية بنات', 'name_en' => 'Girls Shoes']
        );

        /**
         * level3 of Girls Shoes
         */
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'أحذية رياضية', 'name_en' => 'Sneakers', "description" => "description",
                "activation" => true, "category_id" => $girlsShoes->id, 'packing_unit_id' => 8
            ],
            ['name_ar' => 'أحذية رياضية', 'name_en' => 'Sneakers']
        );

        Category::query()->updateOrCreate(
            [
                'name_ar' => 'أحذية رياضية وخارجية', 'name_en' => 'Athletic & Outdoor Shoes',
                "description" => "description", "activation" => true, "category_id" => $girlsShoes->id, 'packing_unit_id' => 8
            ],
            ['name_ar' => 'أحذية رياضية وخارجية', 'name_en' => 'Athletic & Outdoor Shoes']
        );

        Category::query()->updateOrCreate(
            [
                'name_ar' => 'جزمات', 'name_en' => 'Boots', "description" => "description",
                "activation" => true, "category_id" => $girlsShoes->id, 'packing_unit_id' => 8
            ],
            ['name_ar' => 'جزمات', 'name_en' => 'Boots']
        );

        Category::query()->updateOrCreate(
            [
                'name_ar' => 'شباشب', 'name_en' => 'Slippers', "description" => "description",
                "activation" => true, "category_id" => $girlsShoes->id, 'packing_unit_id' => 8
            ],
            ['name_ar' => 'شباشب', 'name_en' => 'Slippers']
        );

        Category::query()->updateOrCreate(
            [
                'name_ar' => 'صنادل الموضة', 'name_en' => 'Fashion Sandals', "description" => "description",
                "activation" => true, "category_id" => $girlsShoes->id, 'packing_unit_id' => 8
            ],
            ['name_ar' => 'صنادل الموضة', 'name_en' => 'Fashion Sandals']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'قباقيب', 'name_en' => 'Clogs', "description" => "description",
                "activation" => true, "category_id" => $boysShoes->id, 'packing_unit_id' => 8
            ],
            ['name_ar' => 'قباقيب', 'name_en' => 'Clogs']
        );


        $boysCloses = Category::query()->updateOrCreate(
            ['name_ar' => 'ملابس أولاد', 'name_en' => 'Boys Clothing', "description" => "description", "activation" => true, "category_id" => $kidsFashionCategory->id],
            ['name_ar' => 'ملابس أولاد', 'name_en' => 'Boys Clothing']
        );

        $girlsCloses = Category::query()->updateOrCreate(
            ['name_ar' => 'ملابس بنات', 'name_en' => 'Girls Clothing', "description" => "description", "activation" => true, "category_id" => $kidsFashionCategory->id],
            ['name_ar' => 'ملابس بنات', 'name_en' => 'Girls Clothing']
        );

        Category::query()->updateOrCreate(
            [
                'name_ar' => 'فستان', 'name_en' => 'Dress', "description" => "description",
                "activation" => true, "category_id" => $girlsCloses->id, 'packing_unit_id' => 2
            ],
            ['name_ar' => 'فستان', 'name_en' => 'Dress']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'الجزء العلوي', 'name_en' => 'Tops', "description" => "description",
                "activation" => true, "category_id" => $girlsCloses->id, 'packing_unit_id' => 2
            ],
            ['name_ar' => 'الجزء العلوي', 'name_en' => 'Tops']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'تنانير', 'name_en' => 'Skirts', "description" => "description",
                "activation" => true, "category_id" => $girlsCloses->id, 'packing_unit_id' => 3
            ],
            ['name_ar' => 'تنانير', 'name_en' => 'Skirts']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'جينز', 'name_en' => 'Jeans', "description" => "description",
                "activation" => true, "category_id" => $girlsCloses->id, 'packing_unit_id' => 3
            ],
            ['name_ar' => 'جينز', 'name_en' => 'Jeans']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'معاطف', 'name_en' => 'Coats', "description" => "description",
                "activation" => true, "category_id" => $girlsCloses->id, 'packing_unit_id' => 4
            ],
            ['name_ar' => 'معاطف', 'name_en' => 'Coats']
        );


        Category::query()->updateOrCreate(
            [
                'name_ar' => 'تيشيرت وقميص', 'name_en' => 'T-Shirts & Shirts', "description" => "description",
                "activation" => true, "category_id" => $boysCloses->id, 'packing_unit_id' => 2
            ],
            ['name_ar' => 'تيشيرت وقميص', 'name_en' => 'T-Shirts & Shirts']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'بنطال وجينز', 'name_en' => 'Pants & Jeans', "description" => "description",
                "activation" => true, "category_id" => $boysCloses->id, 'packing_unit_id' => 6
            ],
            ['name_ar' => 'بنطال وجينز', 'name_en' => 'Pants & Jeans']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'جاكت ومعطف', 'name_en' => 'Jackets & Coats', "description" => "description",
                "activation" => true, "category_id" => $boysCloses->id, 'packing_unit_id' => 4
            ],
            ['name_ar' => 'جاكت ومعطف', 'name_en' => 'Jackets & Coats']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'بدل', 'name_en' => 'Suits', "description" => "description",
                "activation" => true, "category_id" => $boysCloses->id, 'packing_unit_id' => 4
            ],
            ['name_ar' => 'بدل', 'name_en' => 'Suits']
        );

        Category::query()->updateOrCreate(
            [
                'name_ar' => 'شورت داخلي', 'name_en' => 'Boxers', "description" => "description",
                "activation" => true, "category_id" => $boysCloses->id, 'packing_unit_id' => 7
            ],
            ['name_ar' => 'شورت داخلي', 'name_en' => 'Boxers']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'Trunks', 'name_en' => 'Trunks', "description" => "description",
                "activation" => true, "category_id" => $boysCloses->id, 'packing_unit_id' => 7
            ],
            ['name_ar' => 'Trunks', 'name_en' => 'Trunks']
        );
        Category::query()->updateOrCreate(
            [
                'name_ar' => 'Lounge wear', 'name_en' => 'Lounge wear', "description" => "description",
                "activation" => true, "category_id" => $boysCloses->id, 'packing_unit_id' => 7
            ],
            ['name_ar' => 'Lounge wear', 'name_en' => 'Lounge wear']
        );


        //////////////////////////////////////////  End of Main Category Kids Fashion //////////////////////////////////////////


        //////////////////////////////////////////  Start of Main Category Fashion Accessories //////////////////////////////////////////
        //        $FashionAccessoriesCategory = Category::query()->updateOrCreate(
        //            ['name_ar' => 'اكسسوارات الموضة', 'name_en' => 'Fashion Accessories', "description" => "description", "activation" => true],
        //            ['name_ar' => 'اكسسوارات الموضة', 'name_en' => 'Fashion Accessories']
        //        );
        //
        //        Category::query()->updateOrCreate(
        //            ['name_ar' => 'نظارة شمسيه', 'name_en' => 'Sunglasses', "description" => "description", "activation" => true, "category_id" => $FashionAccessoriesCategory->id],
        //            ['name_ar' => 'نظارة شمسيه', 'name_en' => 'Sunglasses']
        //        );
        //        //  Start of Sub Sub  categories of Womens Bag Sub Category
        //        $womensBag = Category::query()->updateOrCreate(
        //            ['name_ar' => 'حقيبة نسائية', 'name_en' => 'Women Bag', "description" => "description", "activation" => true, "category_id" => $FashionAccessoriesCategory->id],
        //            ['name_ar' => 'حقيبة نسائية', 'name_en' => 'Women Bag']
        //        );
        //        Category::query()->updateOrCreate(
        //            ['name_ar' => 'حقيبة ظهر', 'name_en' => 'Back Bag', "description" => "description", "activation" => true, "category_id" => $FashionAccessoriesCategory->id],
        //            ['name_ar' => 'حقيبة ظهر', 'name_en' => 'Back Bag']
        //        );
        //        Category::query()->updateOrCreate(
        //            ['name_ar' => 'حقيبة رجالي ومحفظة', 'name_en' => 'Mens Bag and Wallet', "description" => "description", "activation" => true, "category_id" => $FashionAccessoriesCategory->id],
        //            ['name_ar' => 'حقيبة رجالي ومحفظة', 'name_en' => 'Mens Bag and Wallet']
        //        );


        /**
         * level3
         */
        //        Category::query()->updateOrCreate(
        //            ['name_ar' => 'ساعات نسائية', 'name_en' => 'Womens Watches', "description" => "description", "activation" => true, "category_id" => $FashionAccessoriesCategory->id],
        //            ['name_ar' => 'ساعات نسائية', 'name_en' => 'Womens Watches']
        //        );
        //        Category::query()->updateOrCreate(
        //            ['name_ar' => 'ساعة رجالي', 'name_en' => 'Mens Watch', "description" => "description", "activation" => true, "category_id" => $FashionAccessoriesCategory->id],
        //            ['name_ar' => 'ساعة رجالي', 'name_en' => 'Mens Watch']
        //        );
        //////////////////////////////////////////  End of Main Category Fashion Accessories //////////////////////////////////////////


        //////////////////////////////////////////  Start of Main Category Fabric Category //////////////////////////////////////////
        //        $FabricCategory = Category::query()->updateOrCreate(
        //            ['name_ar' => 'قماش', 'name_en' => 'Fabric', "description" => "description", "activation" => true],
        //            ['name_ar' => 'قماش', 'name_en' => 'Fabric']
        //        );
        //        Category::query()->updateOrCreate(
        //            ['name_ar' => 'الحرير', 'name_en' => 'Silk', "description" => "description", "activation" => true, "category_id" => $FabricCategory->id],
        //            ['name_ar' => 'الحرير', 'name_en' => 'Silk']
        //        );
        //        Category::query()->updateOrCreate(
        //            ['name_ar' => 'قماش وألياف بوليستر', 'name_en' => 'Fabric and Fiber Polyester', "description" => "description", "activation" => true, "category_id" => $FabricCategory->id],
        //            ['name_ar' => 'قماش وألياف بوليستر', 'name_en' => 'Fabric and Fiber Polyester']
        //        );
        //        //////////////////////////////////////////  End of Main Category Fabric Category //////////////////////////////////////////
        //        ///
        //        Category::query()->updateOrCreate(
        //            ['name_ar' => 'أحذية أولاد', 'name_en' => 'Boys Shoes', "description" => "description", "activation" => true, "category_id" => $kidsFashionCategory->id],
        //            ['name_ar' => 'أحذية أولاد', 'name_en' => 'Boys Shoes']
        //        );
        //        Category::query()->updateOrCreate(
        //            ['name_ar' => 'أحذية بنات', 'name_en' => 'Girls Shoes', "description" => "description", "activation" => true, "category_id" => $kidsFashionCategory->id],
        //            ['name_ar' => 'أحذية بنات', 'name_en' => 'Girls Shoes']
        //        );
    }

    /**
     * create Brands
     */
    protected function createBrands()
    {
        $parent_categories_ids = Category::query()->whereNull('category_id')->get()->pluck('id')->toArray();
        $sub_categories_ids = Category::query()->whereIn('category_id', $parent_categories_ids)->get()->pluck('id')->toArray();
        $sub_sub_categories_ids = Category::query()->whereIn('category_id', $sub_categories_ids)->get()->pluck('id')->toArray();

        $fatrinaz = Brand::query()->updateOrCreate(
            ['name_ar' => 'فاتريناز', 'name_en' => 'fatrinaz', "activation" => true],
            ['name_ar' => 'فاتريناز', 'name_en' => 'fatrinaz']
        );
        $fatrinaz->brand_category()->attach($sub_sub_categories_ids);

        $Zaam = Brand::query()->updateOrCreate(
            ['name_ar' => 'زام ديزاين', 'name_en' => 'Zaam Design', "activation" => true],
            ['name_ar' => 'زام ديزاين', 'name_en' => 'Zaam Design']
        );
        $Zaam->brand_category()->attach($sub_sub_categories_ids);

        $Rafeya = Brand::query()->updateOrCreate(
            ['name_ar' => 'رَافية', 'name_en' => 'Rafeya', "activation" => true],
            ['name_ar' => 'رَافية', 'name_en' => 'Rafeya']
        );
        $Rafeya->brand_category()->attach($sub_sub_categories_ids);

        $hm = Brand::query()->updateOrCreate(
            ['name_ar' => 'اتش اند إم', 'name_en' => 'hm', "activation" => true],
            ['name_ar' => 'اتش اند إم', 'name_en' => 'hm']
        );
        $hm->brand_category()->attach($sub_sub_categories_ids);

        Brand::query()->updateOrCreate(
            ['name_ar' => 'زارا', 'name_en' => 'Zara', "activation" => true],
            ['name_ar' => 'زارا', 'name_en' => 'Zara']
        );
        $Zaam->brand_category()->attach($sub_sub_categories_ids);

        $Timberland = Brand::query()->updateOrCreate(
            ['name_ar' => 'تمبرلاند', 'name_en' => 'Timberland', "activation" => true],
            ['name_ar' => 'تمبرلاند', 'name_en' => 'Timberland']
        );
        $Timberland->brand_category()->attach($sub_sub_categories_ids);

        $Farfetch = Brand::query()->updateOrCreate(
            ['name_ar' => 'دولتشي أند غابانا', 'name_en' => 'Farfetch', "activation" => true],
            ['name_ar' => 'دولتشي أند غابانا', 'name_en' => 'Farfetch']
        );
        $Farfetch->brand_category()->attach($sub_sub_categories_ids);
    }

    /**
     * create ٍSizes
     */
    protected function creatSizes()
    {
        $parent_categories_ids = Category::query()->whereNull('category_id')->get()->pluck('id')->toArray();
        $sub_categories_ids = Category::query()->whereIn('category_id', $parent_categories_ids)->get()->pluck('id')->toArray();
        $sub_sub_categories_ids = Category::query()->whereIn('category_id', $sub_categories_ids)->get()->pluck('id')->toArray();

        $xs = Size::query()->updateOrCreate(
            ['size' => 'XS'],
            ['size' => 'XS']
        );
        $xs->categories()->attach($sub_sub_categories_ids);

        $s = Size::query()->updateOrCreate(
            ['size' => 'S'],
            ['size' => 'S']
        );
        $s->categories()->attach($sub_sub_categories_ids);

        $m = Size::query()->updateOrCreate(
            ['size' => 'M'],
            ['size' => 'M']
        );
        $m->categories()->attach($sub_sub_categories_ids);

        $l = Size::query()->updateOrCreate(
            ['size' => 'L'],
            ['size' => 'L']
        );
        $l->categories()->attach($sub_sub_categories_ids);

        $xl = Size::query()->updateOrCreate(
            ['size' => 'XL'],
            ['size' => 'XL']
        );
        $xl->categories()->attach($sub_sub_categories_ids);

        $xxl = Size::query()->updateOrCreate(
            ['size' => 'XXL'],
            ['size' => 'XXL']
        );
        $xxl->categories()->attach($sub_sub_categories_ids);
    }

    /**
     * create ٍSize Types
     */
    protected function creatSizeTypes()
    {
        SizeType::query()->updateOrCreate(
            ['type_en' => ASizeTypes::NUMBERS, 'type_ar' => 'أرقام'],
            ['type_en' => ASizeTypes::NUMBERS, 'type_ar' => 'أرقام']
        );

        SizeType::query()->updateOrCreate(
            ['type_en' => ASizeTypes::TEXT, 'type_ar' => 'نص'],
            ['type_en' => ASizeTypes::TEXT, 'type_ar' => 'نص']
        );
    }

    /**
     * create ٍColors
     */
    protected function creatColors()
    {
        Color::query()->updateOrCreate(
            ['name_en' => 'White', 'name_ar' => 'أبيض', 'hex' => '#FFFFFF'],
            ['name_en' => 'White', 'name_ar' => 'أبيض', 'hex' => '#FFFFFF']
        );
        Color::query()->updateOrCreate(
            ['name_en' => 'Red', 'name_ar' => 'أحمر', 'hex' => '#FF0000'],
            ['name_en' => 'Red', 'name_ar' => 'أحمر', 'hex' => '#FF0000']
        );
        Color::query()->updateOrCreate(
            ['name_en' => 'Purple', 'name_ar' => 'بنفسجي', 'hex' => '#800080'],
            ['name_en' => 'Purple', 'name_ar' => 'بنفسجي', 'hex' => '#800080']
        );
        Color::query()->updateOrCreate(
            ['name_en' => 'Green', 'name_ar' => 'أخضر', 'hex' => '#008000'],
            ['name_en' => 'Green', 'name_ar' => 'أخضر', 'hex' => '#008000']
        );
        Color::query()->updateOrCreate(
            ['name_en' => 'Blue', 'name_ar' => 'أزرق', 'hex' => '#0000FF'],
            ['name_en' => 'Blue', 'name_ar' => 'أزرق', 'hex' => '#0000FF']
        );
        Color::query()->updateOrCreate(
            ['name_en' => 'Black', 'name_ar' => 'أسود', 'hex' => '#000000'],
            ['name_en' => 'Black', 'name_ar' => 'أسود', 'hex' => '#000000']
        );
    }

    /**
     * create Order Status
     */
    protected function creatOrderStatus()
    {
        OrderStatus::query()->updateOrCreate(
            ['status_en' => 'Pending', 'status_ar' => 'قيد الانتظار'],
            ['status_en' => 'Pending', 'status_ar' => 'قيد الانتظار']
        );
        OrderStatus::query()->updateOrCreate(
            ['status_en' => 'in progress', 'status_ar' => 'قيد التنفيذ'],
            ['status_en' => 'in progress', 'status_ar' => 'قيد التنفيذ']
        );
        OrderStatus::query()->updateOrCreate(
            ['status_en' => 'received', 'status_ar' => 'تم الاستلام'],
            ['status_en' => 'received', 'status_ar' => 'تم الاستلام']
        );
        OrderStatus::query()->updateOrCreate(
            ['status_en' => 'canceled', 'status_ar' => 'ملغى'],
            ['status_en' => 'canceled', 'status_ar' => 'ملغى']
        );
        OrderStatus::query()->updateOrCreate(
            ['status_en' => 'rejected', 'status_ar' => 'مرفوض'],
            ['status_en' => 'rejected', 'status_ar' => 'مرفوض']
        );

        OrderStatus::query()->updateOrCreate(
            ['status_en' => 'Shipping', 'status_ar' => 'جاري الشحن'],
            ['status_en' => 'Shipping', 'status_ar' => 'جاري الشحن']
        );
    }

    /**
     * create Store Types
     */
    protected function creatStoreTypes()
    {
        StoreType::query()->updateOrCreate(
            [
                'name_en' => 'Retail',
                'name_ar' => 'قطاعي',
                'description_ar' => 'وصف متجر قطاعي',
                'description_en' => 'descriptios store retail'
            ],
        );
        StoreType::query()->updateOrCreate(
            ['name_en' => 'Supplier', 'name_ar' => 'جملة'],
            [
                'name_en' => 'Supplier',
                'name_ar' => 'جملة',
                'description_ar' => 'وصف متجر جملة',
                'description_en' => 'descriptios store supplier'
            ],
        );
    }

    /**
     * create Transaction Types
     */
    protected function creatTransactionTypes()
    {
        DB::table('transaction_types')
            ->updateOrInsert(
                ['name_en' => 'PRODUCT', 'name_ar' => 'منتج'],
                ['name_en' => 'PRODUCT', 'name_ar' => 'منتج']
            );
        DB::table('transaction_types')
            ->updateOrInsert(
                ['name_en' => 'ORDER', 'name_ar' => 'طلب'],
                ['name_en' => 'ORDER', 'name_ar' => 'طلب']
            );
        DB::table('transaction_types')
            ->updateOrInsert(
                ['name_en' => 'INVENTORY', 'name_ar' => 'المخزن'],
                ['name_en' => 'INVENTORY', 'name_ar' => 'المخزن']
            );

        DB::table('transaction_types')
            ->updateOrInsert(
                ['name_en' => 'SALE', 'name_ar' => 'بيع'],
                ['name_en' => 'SALE', 'name_ar' => 'بيع']
            );
    }

    /**
     * creat Packing Units
     */
    protected function creatPackingUnits()
    {

        PackingUnit::query()->updateOrCreate(
            ['name_en' => 'Dasta', 'name_ar' => 'دسته'],
            ['name_en' => 'Dasta', 'name_ar' => 'دسته']
        );
        PackingUnit::query()->updateOrCreate(
            ['name_en' => 'Shirt', 'name_ar' => 'قميص'],
            ['name_en' => 'Shirt', 'name_ar' => 'قميص']
        );

        PackingUnit::query()->updateOrCreate(
            ['name_en' => 'T-shirt', 'name_ar' => 'نيشيرت'],
            ['name_en' => 'T-shirt', 'name_ar' => 'نيشيرت']
        );

        PackingUnit::query()->updateOrCreate(
            ['name_en' => 'Jacket', 'name_ar' => 'جاكيت'],
            ['name_en' => 'Jacket', 'name_ar' => 'جاكيت']
        );


        PackingUnit::query()->updateOrCreate(
            ['name_en' => 'Pullover', 'name_ar' => 'بلوفر'],
            ['name_en' => 'Pullover', 'name_ar' => 'بلوفر']
        );

        PackingUnit::query()->updateOrCreate(
            ['name_en' => 'Pants', 'name_ar' => 'بنطلون'],
            ['name_en' => 'Pants', 'name_ar' => 'بنطلون']
        );

        PackingUnit::query()->updateOrCreate(
            ['name_en' => 'Short', 'name_ar' => 'شورت'],
            ['name_en' => 'Short', 'name_ar' => 'شورت']
        );

        PackingUnit::query()->updateOrCreate(
            ['name_en' => 'Shoes', 'name_ar' => 'جزمه'],
            ['name_en' => 'Shoes', 'name_ar' => 'جزمه']
        );
    }

    /**
     * creat Days Of Weeks
     */
    protected function creatDaysOfWeeks()
    {

        DaysOfWeek::query()->updateOrCreate(
            ['name_en' => 'Saturday', 'name_ar' => 'السبت'],
            ['name_en' => 'Saturday', 'name_ar' => 'السبت']
        );
        DaysOfWeek::query()->updateOrCreate(
            ['name_en' => 'Sunday', 'name_ar' => 'الأحد'],
            ['name_en' => 'Sunday', 'name_ar' => 'الأحد']
        );
        DaysOfWeek::query()->updateOrCreate(
            ['name_en' => 'Monday', 'name_ar' => 'الاثنين'],
            ['name_en' => 'Monday', 'name_ar' => 'الاثنين']
        );
        DaysOfWeek::query()->updateOrCreate(
            ['name_en' => 'Tuesday', 'name_ar' => 'الثلاثاء'],
            ['name_en' => 'Tuesday', 'name_ar' => 'الثلاثاء']
        );
        DaysOfWeek::query()->updateOrCreate(
            ['name_en' => 'Wednesday', 'name_ar' => 'الأربعاء'],
            ['name_en' => 'Wednesday', 'name_ar' => 'الأربعاء']
        );
        DaysOfWeek::query()->updateOrCreate(
            ['name_en' => 'Thursday', 'name_ar' => 'الخميس'],
            ['name_en' => 'Thursday', 'name_ar' => 'الخميس']
        );
        DaysOfWeek::query()->updateOrCreate(
            ['name_en' => 'Friday', 'name_ar' => 'الجمعه'],
            ['name_en' => 'Friday', 'name_ar' => 'الجمعه']
        );
    }
}
