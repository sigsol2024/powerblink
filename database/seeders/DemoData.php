<?php

namespace Database\Seeders;

/**
 * Demo storefront data for Vogue Dress 4 Less.
 *
 * NOTE: the catalog table is still named `vehicles` for historical reasons (this app evolved
 * from an autos template). All UI code treats a `Vehicle` as "a product". The legacy car-shaped
 * columns are slated for removal in the Phase 9 destructive migration; only the lean product
 * fields (title, slug, price, stock, features, description, images, category, sku) are kept.
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
     * Lean product seed rows used by VehiclesSeeder. Fields map to the surviving
     * `vehicles` columns (title, price, stock, features, description, images, category, sku).
     *
     * @return array<int, array<string, mixed>>
     */
    public static function vehicles(): array
    {
        return [
            [
                'title' => 'Omi Silk Wrap',
                'category' => 'Dress',
                'price' => 84000,
                'stock' => 12,
                'sku' => 'VD-OMI-SLK',
                'features' => ['Heavy-weight mulberry silk', 'Asymmetric wrap detail', 'Hand-finished hem', 'Ethically produced in Lagos'],
                'description' => "The Omi Silk Wrap is a testament to artisanal precision. Crafted from 100% heavy-weight mulberry silk, this silhouette features asymmetric draping inspired by traditional West African wrapping techniques, reimagined for the modern global landscape.",
                'images' => self::gallery('omi-silk-wrap'),
            ],
            [
                'title' => 'Adira Sculpted Coat',
                'category' => 'Coat',
                'price' => 145000,
                'stock' => 6,
                'sku' => 'VD-ADIRA-COAT',
                'features' => ['Structured Nigerian embroidery', 'Architectural folds', 'Wool-blend outer', 'Tonal lining'],
                'description' => 'A structured statement coat with intricate Nigerian embroidery and architectural folds. Tailored to drape with deliberate weight.',
                'images' => self::gallery('adira-coat'),
            ],
            [
                'title' => 'Kola Tonal Silk Dress',
                'category' => 'Dress',
                'price' => 92000,
                'stock' => 9,
                'sku' => 'VD-KOLA-SLK',
                'features' => ['Tonal geometric pattern', 'West African textile motif', 'Silk crepe', 'Hand-finished seams'],
                'description' => 'Luxury silk dress featuring subtle, tonal geometric patterns inspired by West African textiles.',
                'images' => self::gallery('kola-silk'),
            ],
            [
                'title' => 'Ada Leather Handbag',
                'category' => 'Bag',
                'price' => 68000,
                'stock' => 14,
                'sku' => 'VD-ADA-BAG',
                'features' => ['Premium full-grain leather', 'Artisanal gold hardware', 'Suede-lined interior', 'Detachable strap'],
                'description' => 'A premium leather handbag with artisanal gold hardware and a sand-toned palette. Silent luxury, built to last.',
                'images' => self::gallery('ada-handbag'),
            ],
            [
                'title' => 'Ife Tailored Trouser',
                'category' => 'Trouser',
                'price' => 58000,
                'stock' => 18,
                'sku' => 'VD-IFE-TRS',
                'features' => ['Heavy-weight woven cotton', 'Sculptural rear pocket', 'Unisex fit', 'Hidden hook closure'],
                'description' => 'Minimalist unisex trouser tailored in heavy-weight woven cotton. Precision-cut for everyday wear.',
                'images' => self::gallery('ife-trouser'),
            ],
            [
                'title' => 'Oba Indigo Blazer',
                'category' => 'Blazer',
                'price' => 120000,
                'stock' => 7,
                'sku' => 'VD-OBA-BLZ',
                'features' => ['Structured shoulder', 'Cultural lapel motif', 'Deep indigo wool', 'Bemberg lining'],
                'description' => 'Structured blazer with subtle cultural motifs woven into the lapel. Deep indigo for evening or day.',
                'images' => self::gallery('oba-blazer'),
            ],
            [
                'title' => 'Zola Linen Shirt',
                'category' => 'Shirt',
                'price' => 38000,
                'stock' => 24,
                'sku' => 'VD-ZOLA-SHIRT',
                'features' => ['Translucent French linen', 'Oversized cut', 'Mother-of-pearl buttons', 'Drop shoulder'],
                'description' => 'Flowing oversized linen shirt designed to catch the light. Ethereal and quietly dramatic.',
                'images' => self::gallery('zola-linen-shirt'),
            ],
            [
                'title' => 'Lara Heeled Mule',
                'category' => 'Footwear',
                'price' => 58000,
                'stock' => 16,
                'sku' => 'VD-LARA-MULE',
                'features' => ['Hand-burnished leather', 'Architectural heel', 'Leather sole', 'Anti-slip rubber tip'],
                'description' => 'Architectural heeled mule with a minimal strap and hand-burnished leather upper.',
                'images' => self::gallery('lara-mule'),
            ],
            [
                'title' => 'Aje Woven Scarf',
                'category' => 'Accessory',
                'price' => 28000,
                'stock' => 30,
                'sku' => 'VD-AJE-SCARF',
                'features' => ['Wool & silk blend', 'Oversized geometric weave', 'Hand-fringed edges'],
                'description' => 'Premium wool scarf with a subtle, oversized geometric weave. Luxurious weight and drape.',
                'images' => self::gallery('aje-scarf'),
            ],
            [
                'title' => 'Onyx Leather Clutch',
                'category' => 'Bag',
                'price' => 45000,
                'stock' => 11,
                'sku' => 'VD-ONYX-CLUTCH',
                'features' => ['Polished leather shell', 'Brass hinge clasp', 'Suede interior'],
                'description' => 'Sculpted leather clutch with brass hardware and a clean, hand-finished shell.',
                'images' => self::gallery('leather-shell-clutch'),
            ],
            [
                'title' => 'Knit Sculpt Top',
                'category' => 'Top',
                'price' => 62000,
                'stock' => 13,
                'sku' => 'VD-KNIT-TOP',
                'features' => ['Artisanal knit', 'Sculptural neckline', 'Neutral palette'],
                'description' => 'Artisanal knit top with a sculptural neckline. Soft hand, considered drape.',
                'images' => self::gallery('knit-sculpt-top'),
            ],
        ];
    }
}
