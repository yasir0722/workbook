<?php

namespace App\Services;

use App\Contracts\ContentSourceInterface;
use App\Models\FacebookPage;
use Illuminate\Support\Carbon;

class MockFacebookContentSource implements ContentSourceInterface
{
    public function synchronize(): void
    {
        foreach ($this->pages() as $pageData) {
            $posts = $pageData['posts'];
            unset($pageData['posts']);

            $page = FacebookPage::query()->updateOrCreate(
                ['username' => $pageData['username']],
                $pageData,
            );

            foreach ($posts as $postData) {
                $media = $postData['media'];
                unset($postData['media']);

                $post = $page->posts()->updateOrCreate(
                    ['facebook_post_id' => $postData['facebook_post_id']],
                    $postData,
                );

                $post->media()->delete();
                $post->media()->createMany($media);
            }
        }
    }

    /**
     * Development-only content that exercises the reader's display states.
     *
     * @return array<int, array<string, mixed>>
     */
    private function pages(): array
    {
        $now = Carbon::now();

        return [
            [
                'facebook_page_id' => 'mock-pasti-malaysia',
                'name' => 'PASTI Malaysia',
                'username' => 'pasti-malaysia',
                'url' => 'https://example.com/mock/pasti-malaysia',
                'category' => 'Education',
                'enabled' => true,
                'sort_order' => 1,
                'last_fetched_at' => $now,
                'posts' => [
                    [
                        'facebook_post_id' => 'mock-pasti-001',
                        'message' => 'Pendaftaran sesi baharu kini dibuka. Sila hubungi PASTI kawasan anda untuk maklumat lanjut.',
                        'permalink' => 'https://example.com/mock/pasti-001',
                        'published_at' => $now->copy()->subMinutes(18),
                        'media' => [],
                    ],
                    [
                        'facebook_post_id' => 'mock-pasti-002',
                        'message' => 'Aktiviti pembelajaran hari ini memberi peluang kepada murid untuk meneroka, bertanya, dan berkongsi idea bersama rakan-rakan.',
                        'permalink' => 'https://example.com/mock/pasti-002',
                        'published_at' => $now->copy()->subHours(5),
                        'media' => [
                            [
                                'media_type' => 'image',
                                'media_url' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1200&q=80',
                                'thumbnail_url' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=500&q=75',
                                'sort_order' => 1,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'facebook_page_id' => 'mock-kpm',
                'name' => 'KPM',
                'username' => 'kpm',
                'url' => 'https://example.com/mock/kpm',
                'category' => 'Government',
                'enabled' => true,
                'sort_order' => 2,
                'last_fetched_at' => $now,
                'posts' => [
                    [
                        'facebook_post_id' => 'mock-kpm-001',
                        'message' => 'Makluman: Semakan keputusan permohonan boleh dibuat melalui saluran rasmi yang dinyatakan dalam pengumuman ini.',
                        'permalink' => 'https://example.com/mock/kpm-001',
                        'published_at' => $now->copy()->subMinutes(42),
                        'media' => [],
                    ],
                    [
                        'facebook_post_id' => 'mock-kpm-002',
                        'message' => 'Perkongsian ringkas mengenai pelaksanaan program pendidikan dan sokongan pembelajaran di sekolah.',
                        'permalink' => 'https://example.com/mock/kpm-002',
                        'published_at' => $now->copy()->subHours(8),
                        'media' => [
                            [
                                'media_type' => 'image',
                                'media_url' => 'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1200&q=80',
                                'thumbnail_url' => 'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=500&q=75',
                                'sort_order' => 1,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'facebook_page_id' => 'mock-jakim',
                'name' => 'JAKIM',
                'username' => 'jakim',
                'url' => 'https://example.com/mock/jakim',
                'category' => 'Religion',
                'enabled' => true,
                'sort_order' => 3,
                'last_fetched_at' => $now,
                'posts' => [
                    [
                        'facebook_post_id' => 'mock-jakim-001',
                        'message' => 'Program komuniti hujung minggu ini terbuka kepada orang ramai. Rujuk poster untuk masa dan lokasi.',
                        'permalink' => 'https://example.com/mock/jakim-001',
                        'published_at' => $now->copy()->subHours(2),
                        'media' => [
                            [
                                'media_type' => 'image',
                                'media_url' => 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?auto=format&fit=crop&w=1200&q=80',
                                'thumbnail_url' => 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?auto=format&fit=crop&w=500&q=75',
                                'sort_order' => 1,
                            ],
                            [
                                'media_type' => 'image',
                                'media_url' => 'https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=1200&q=80',
                                'thumbnail_url' => 'https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=500&q=75',
                                'sort_order' => 2,
                            ],
                        ],
                    ],
                    [
                        'facebook_post_id' => 'mock-jakim-002',
                        'message' => null,
                        'permalink' => null,
                        'published_at' => $now->copy()->subDay(),
                        'media' => [
                            [
                                'media_type' => 'image',
                                'media_url' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=1200&q=80',
                                'thumbnail_url' => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?auto=format&fit=crop&w=500&q=75',
                                'sort_order' => 1,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'facebook_page_id' => 'mock-jpj',
                'name' => 'JPJ',
                'username' => 'jpj',
                'url' => 'https://example.com/mock/jpj',
                'category' => 'Government',
                'enabled' => true,
                'sort_order' => 4,
                'last_fetched_at' => $now,
                'posts' => [
                    [
                        'facebook_post_id' => 'mock-jpj-001',
                        'message' => 'Notis ringkas: Kaunter bergerak akan beroperasi mengikut jadual yang dikemas kini.',
                        'permalink' => 'https://example.com/mock/jpj-001',
                        'published_at' => $now->copy()->subHours(3),
                        'media' => [],
                    ],
                    [
                        'facebook_post_id' => 'mock-jpj-002',
                        'message' => 'Sila pastikan dokumen lengkap sebelum hadir ke kaunter. Maklumat ini adalah data contoh untuk pembangunan aplikasi.',
                        'permalink' => 'https://example.com/mock/jpj-002',
                        'published_at' => $now->copy()->subDays(2),
                        'media' => [],
                    ],
                ],
            ],
            [
                'facebook_page_id' => 'mock-local-community',
                'name' => 'Local Community',
                'username' => 'local-community',
                'url' => 'https://example.com/mock/local-community',
                'category' => 'Community',
                'enabled' => true,
                'sort_order' => 5,
                'last_fetched_at' => $now,
                'posts' => [
                    [
                        'facebook_post_id' => 'mock-community-001',
                        'message' => 'Gotong-royong komuniti akan diadakan pada pagi Sabtu. Semua penduduk dialu-alukan untuk menyertai.',
                        'permalink' => 'https://example.com/mock/community-001',
                        'published_at' => $now->copy()->subMinutes(55),
                        'media' => [
                            [
                                'media_type' => 'image',
                                'media_url' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=1200&q=80',
                                'thumbnail_url' => 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?auto=format&fit=crop&w=500&q=75',
                                'sort_order' => 1,
                            ],
                        ],
                    ],
                    [
                        'facebook_post_id' => 'mock-community-002',
                        'message' => 'Terima kasih kepada semua sukarelawan yang membantu menjayakan aktiviti minggu lalu.',
                        'permalink' => null,
                        'published_at' => $now->copy()->subDays(3),
                        'media' => [],
                    ],
                ],
            ],
            [
                'facebook_page_id' => 'mock-technology',
                'name' => 'Technology',
                'username' => 'technology',
                'url' => 'https://example.com/mock/technology',
                'category' => 'Technology',
                'enabled' => true,
                'sort_order' => 6,
                'last_fetched_at' => $now,
                'posts' => [
                    [
                        'facebook_post_id' => 'mock-technology-001',
                        'message' => 'Pembangunan produk yang baik bermula dengan masalah yang jelas, maklum balas yang berguna, dan perubahan kecil yang boleh diuji.',
                        'permalink' => 'https://example.com/mock/technology-001',
                        'published_at' => $now->copy()->subHours(4),
                        'media' => [
                            [
                                'media_type' => 'image',
                                'media_url' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=80',
                                'thumbnail_url' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=500&q=75',
                                'sort_order' => 1,
                            ],
                        ],
                    ],
                    [
                        'facebook_post_id' => 'mock-technology-002',
                        'message' => 'Nota pembangunan: Antara muka yang tenang membantu pengguna mencari maklumat tanpa terganggu. Ini ialah contoh teks yang lebih panjang untuk menguji pembalutan perenggan, kebolehbacaan, dan ruang di dalam kad siaran. Tiada metrik penglibatan atau kandungan cadangan dipaparkan dalam pembaca ini.',
                        'permalink' => 'https://example.com/mock/technology-002',
                        'published_at' => $now->copy()->subDays(4),
                        'media' => [],
                    ],
                ],
            ],
        ];
    }
}
