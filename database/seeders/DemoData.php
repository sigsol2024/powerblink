<?php

namespace Database\Seeders;

/**
 * Demo storefront data for Vogue Dress 4 Less.
 *
 * NOTE: the catalog table is named `vehicles` for historical reasons (this app evolved from an
 * autos template). All public-facing UIs simply read a `Vehicle` as "a product". For a fashion
 * storefront we map the original car columns to fashion-friendly values:
 *   - `make`         -> design/atelier label (brand)
 *   - `model`        -> silhouette / piece name
 *   - `body_type`    -> category (Dress / Coat / Trouser / Bag / Footwear / Accessory)
 *   - `exterior_color` -> primary colorway (shown on cards)
 *   - `interior_color` -> lining / accent colorway
 *   - `engine_size` / `mileage` / `transmission` / `drive` / `fuel_type` are kept as harmless
 *     neutral values so existing listing-option logic continues to validate. They're not shown
 *     anywhere on the storefront.
 *
 * Gallery images live in `public/asset/images/demo-products/` and are committed to the repo so
 * `php artisan db:seed` works on shared hosting without any external download step.
 */
final class DemoData
{
    public const ADMIN_EMAIL = 'admin@example.com';

    public const USER_EMAIL = 'demo@example.com';

    public const DEFAULT_PASSWORD = 'password';

    /**
     * Single-image gallery (one curated editorial shot per product).
     *
     * @return list<string>
     */
    public static function gallery(string $imageBasename): array
    {
        return ['asset/images/demo-products/'.$imageBasename.'.jpg'];
    }

    /**
     * @return array<string, array{name: string, email: string, role: string}>
     */
    public static function users(): array
    {
        return [
            'admin' => [
                'name' => 'Demo Admin',
                'email' => self::ADMIN_EMAIL,
                'role' => 'admin',
            ],
            'user' => [
                'name' => 'Demo User',
                'email' => self::USER_EMAIL,
                'role' => 'user',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function vehicles(): array
    {
        $atelier = 'Vogue Atelier';
        $year = (int) date('Y');

        // Neutral filler so the legacy car-shaped listing-option catalog accepts each row.
        $neutralCar = [
            'year' => $year,
            'mileage' => 0,
            'fuel_type' => 'N/A',
            'transmission' => 'N/A',
            'drive' => 'N/A',
            'condition' => 'new',
            'engine_size' => null,
            'country' => 'Nigeria',
        ];

        return [
            [
                'title' => 'Omi Silk Wrap',
                'make' => $atelier,
                'model' => 'Silk Wrap',
                'body_type' => 'Dress',
                'price' => 84000,
                'street_address' => 'Lagos, Nigeria',
                'features' => ['Heavy-weight mulberry silk', 'Asymmetric wrap detail', 'Hand-finished hem', 'Ethically produced in Lagos'],
                'exterior_color' => 'Onyx',
                'interior_color' => 'Silk lining',
                'description' => "The Omi Silk Wrap is a testament to artisanal precision. Crafted from 100% heavy-weight mulberry silk, this silhouette features asymmetric draping inspired by traditional West African wrapping techniques, reimagined for the modern global landscape.",
                'images' => self::gallery('omi-silk-wrap'),
            ] + $neutralCar,
            [
                'title' => 'Adira Sculpted Coat',
                'make' => $atelier,
                'model' => 'Sculpted Coat',
                'body_type' => 'Coat',
                'price' => 145000,
                'street_address' => 'Lagos, Nigeria',
                'features' => ['Structured Nigerian embroidery', 'Architectural folds', 'Wool-blend outer', 'Tonal lining'],
                'exterior_color' => 'Onyx',
                'interior_color' => 'Charcoal',
                'description' => 'A structured statement coat with intricate Nigerian embroidery and architectural folds. Tailored to drape with deliberate weight.',
                'images' => self::gallery('adira-coat'),
            ] + $neutralCar,
            [
                'title' => 'Kola Tonal Silk Dress',
                'make' => $atelier,
                'model' => 'Tonal Silk Dress',
                'body_type' => 'Dress',
                'price' => 92000,
                'street_address' => 'Lagos, Nigeria',
                'features' => ['Tonal geometric pattern', 'West African textile motif', 'Silk crepe', 'Hand-finished seams'],
                'exterior_color' => 'Cream',
                'interior_color' => 'Gold',
                'description' => 'Luxury silk dress featuring subtle, tonal geometric patterns inspired by West African textiles.',
                'images' => self::gallery('kola-silk'),
            ] + $neutralCar,
            [
                'title' => 'Ada Leather Handbag',
                'make' => $atelier,
                'model' => 'Leather Handbag',
                'body_type' => 'Bag',
                'price' => 68000,
                'street_address' => 'Lagos, Nigeria',
                'features' => ['Premium full-grain leather', 'Artisanal gold hardware', 'Suede-lined interior', 'Detachable strap'],
                'exterior_color' => 'Sand',
                'interior_color' => 'Suede',
                'description' => 'A premium leather handbag with artisanal gold hardware and a sand-toned palette. Silent luxury, built to last.',
                'images' => self::gallery('ada-handbag'),
            ] + $neutralCar,
            [
                'title' => 'Ife Tailored Trouser',
                'make' => $atelier,
                'model' => 'Tailored Trouser',
                'body_type' => 'Trouser',
                'price' => 58000,
                'street_address' => 'Lagos, Nigeria',
                'features' => ['Heavy-weight woven cotton', 'Sculptural rear pocket', 'Unisex fit', 'Hidden hook closure'],
                'exterior_color' => 'Bone',
                'interior_color' => 'Cotton',
                'description' => 'Minimalist unisex trouser tailored in heavy-weight woven cotton. Precision-cut for everyday wear.',
                'images' => self::gallery('ife-trouser'),
            ] + $neutralCar,
            [
                'title' => 'Oba Indigo Blazer',
                'make' => $atelier,
                'model' => 'Indigo Blazer',
                'body_type' => 'Blazer',
                'price' => 120000,
                'street_address' => 'Lagos, Nigeria',
                'features' => ['Structured shoulder', 'Cultural lapel motif', 'Deep indigo wool', 'Bemberg lining'],
                'exterior_color' => 'Indigo',
                'interior_color' => 'Bemberg',
                'description' => 'Structured blazer with subtle cultural motifs woven into the lapel. Deep indigo for evening or day.',
                'images' => self::gallery('oba-blazer'),
            ] + $neutralCar,
            [
                'title' => 'Zola Linen Shirt',
                'make' => $atelier,
                'model' => 'Linen Shirt',
                'body_type' => 'Shirt',
                'price' => 38000,
                'street_address' => 'Lagos, Nigeria',
                'features' => ['Translucent French linen', 'Oversized cut', 'Mother-of-pearl buttons', 'Drop shoulder'],
                'exterior_color' => 'Bone',
                'interior_color' => 'Linen',
                'description' => 'Flowing oversized linen shirt designed to catch the light. Ethereal and quietly dramatic.',
                'images' => self::gallery('zola-linen-shirt'),
            ] + $neutralCar,
            [
                'title' => 'Lara Heeled Mule',
                'make' => $atelier,
                'model' => 'Heeled Mule',
                'body_type' => 'Footwear',
                'price' => 58000,
                'street_address' => 'Lagos, Nigeria',
                'features' => ['Hand-burnished leather', 'Architectural heel', 'Leather sole', 'Anti-slip rubber tip'],
                'exterior_color' => 'Onyx',
                'interior_color' => 'Leather',
                'description' => 'Architectural heeled mule with a minimal strap and hand-burnished leather upper.',
                'images' => self::gallery('lara-mule'),
            ] + $neutralCar,
            [
                'title' => 'Aje Woven Scarf',
                'make' => $atelier,
                'model' => 'Woven Scarf',
                'body_type' => 'Accessory',
                'price' => 28000,
                'street_address' => 'Lagos, Nigeria',
                'features' => ['Wool & silk blend', 'Oversized geometric weave', 'Hand-fringed edges'],
                'exterior_color' => 'Slate',
                'interior_color' => 'Wool',
                'description' => 'Premium wool scarf with a subtle, oversized geometric weave. Luxurious weight and drape.',
                'images' => self::gallery('aje-scarf'),
            ] + $neutralCar,
            [
                'title' => 'Onyx Leather Clutch',
                'make' => $atelier,
                'model' => 'Leather Clutch',
                'body_type' => 'Bag',
                'price' => 45000,
                'street_address' => 'Lagos, Nigeria',
                'features' => ['Polished leather shell', 'Brass hinge clasp', 'Suede interior'],
                'exterior_color' => 'Onyx',
                'interior_color' => 'Suede',
                'description' => 'Sculpted leather clutch with brass hardware and a clean, hand-finished shell.',
                'images' => self::gallery('leather-shell-clutch'),
            ] + $neutralCar,
            [
                'title' => 'Knit Sculpt Top',
                'make' => $atelier,
                'model' => 'Sculpt Top',
                'body_type' => 'Top',
                'price' => 62000,
                'street_address' => 'Lagos, Nigeria',
                'features' => ['Artisanal knit', 'Sculptural neckline', 'Neutral palette'],
                'exterior_color' => 'Bone',
                'interior_color' => 'Knit',
                'description' => 'Artisanal knit top with a sculptural neckline. Soft hand, considered drape.',
                'images' => self::gallery('knit-sculpt-top'),
            ] + $neutralCar,
        ];
    }
}
