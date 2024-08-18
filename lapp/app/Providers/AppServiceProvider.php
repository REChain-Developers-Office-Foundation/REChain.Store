<?php

namespace App\Providers;

use App;
use App\Models\Comment;
use App\Models\Report;
use App\Models\Setting;
use App\Models\Submission;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events)
    {
        $this->app->singleton('site_lang', function () {

            // Retrieve settings
            $site_settings = Setting::get();

            foreach ($site_settings as $setting) {
                $settings[$setting->name] = $setting->value;
            }

            return $settings['site_language'];

        });

        // Set default site language
        App::setLocale(app('site_lang'));

        Paginator::useBootstrap();
        $this->loadViewsFrom(__DIR__ . '/views/vendor/frontend', 'frontend');
        $this->loadViewsFrom(__DIR__ . '/views/vendor/rtl-frontend', 'rtl-frontend');
        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {

            $event->menu->addAfter('fullscreen-widget', [
                'key' => 'apps',
                'text' => __('admin.apps'),
                'icon' => 'fas fa-fw fa-mobile-alt mr-1',
                'submenu' => [
                    [
                        'text' => __('admin.apps'),
                        'active' => [env('ADMIN_URL'), env('ADMIN_URL') . '/apps/*/edit', env('ADMIN_URL') . '/versions/*', env('ADMIN_URL') . '/app_translations/*', env('ADMIN_URL') . '/search'],
                        'url' => env('ADMIN_URL') . '/apps',
                    ],
                    [
                        'text' => __('admin.create_app'),
                        'url' => env('ADMIN_URL') . '/apps/create',
                    ],
                ],
            ]);

            $event->menu->addAfter('apps', [
                'key' => 'categories',
                'text' => __('admin.categories'),
                'icon' => 'fas fa-fw fa-bookmark mr-1',
                'submenu' => [
                    [
                        'text' => __('admin.categories'),
                        'active' => [env('ADMIN_URL') . '/categories/*/edit', env('ADMIN_URL') . '/categories/sort'],
                        'url' => env('ADMIN_URL') . '/categories',
                    ],
                    [
                        'text' => __('admin.create_category'),
                        'url' => env('ADMIN_URL') . '/categories/create',
                    ],
                ],
            ]);

            $event->menu->addAfter('categories', [
                'key' => 'platforms',
                'text' => __('admin.platforms'),
                'icon' => 'fab fa-fw fa-windows mr-1',
                'submenu' => [
                    [
                        'text' => __('admin.platforms'),
                        'active' => [env('ADMIN_URL') . '/platforms/*/edit', env('ADMIN_URL') . '/platforms/sort'],
                        'url' => env('ADMIN_URL') . '/platforms',
                    ],
                    [
                        'text' => __('admin.create_platform'),
                        'url' => env('ADMIN_URL') . '/platforms/create',
                    ],
                ],
            ]);

            $event->menu->addAfter('platforms', [
                'key' => 'pages',
                'text' => __('admin.pages'),
                'icon' => 'fas fa-fw fa-file mr-1',
                'submenu' => [
                    [
                        'text' => __('admin.pages'),
                        'active' => [env('ADMIN_URL') . '/pages/*/edit'],
                        'url' => env('ADMIN_URL') . '/pages',
                    ],
                    [
                        'text' => __('admin.create_page'),
                        'url' => env('ADMIN_URL') . '/pages/create',
                    ],
                ],
            ]);

            $event->menu->addAfter('pages', [
                'key' => 'sliders',
                'text' => __('admin.sliders'),
                'icon' => 'fas fa-fw fa-sliders-h mr-1',
                'submenu' => [
                    [
                        'text' => __('admin.sliders'),
                        'active' => [env('ADMIN_URL') . '/sliders/*/edit'],
                        'url' => env('ADMIN_URL') . '/sliders',
                    ],
                    [
                        'text' => __('admin.create_slider'),
                        'url' => env('ADMIN_URL') . '/sliders/create',
                    ],
                ],
            ]);

            $event->menu->addAfter('sliders', [
                'key' => 'topics',
                'text' => __('admin.topics'),
                'icon' => 'fas fa-fw fa-star mr-1',
                'submenu' => [
                    [
                        'text' => __('admin.topics'),
                        'active' => [env('ADMIN_URL') . '/topics/*/edit', env('ADMIN_URL') . '/topic/*'],
                        'url' => env('ADMIN_URL') . '/topics',
                    ],
                    [
                        'text' => __('admin.create_topic'),
                        'url' => env('ADMIN_URL') . '/topics/create',
                    ],
                ],
            ]);

            $event->menu->addAfter('topics', [
                'key' => 'news',
                'text' => __('admin.news'),
                'icon' => 'fas fa-fw fa-font mr-1',
                'submenu' => [
                    [
                        'text' => __('admin.news'),
                        'active' => [env('ADMIN_URL') . '/news/*/edit'],
                        'url' => env('ADMIN_URL') . '/news',
                    ],
                    [
                        'text' => __('admin.create_news'),
                        'url' => env('ADMIN_URL') . '/news/create',
                    ],
                    [
                        'text' => __('admin.categories'),
                        'active' => [env('ADMIN_URL') . '/news-categories*', env('ADMIN_URL') . '/news-categories/*/edit', env('ADMIN_URL') . '/news-categories/sort'],
                        'url' => env('ADMIN_URL') . '/news-categories',
                    ],
                ],
            ]);

            $event->menu->addAfter('news', [
                'key' => 'content_manager',
                'text' => __('admin.content_manager'),
                'icon' => 'fas fa-fw fa-cube mr-1',
                'submenu' => [
                    [
                        'text' => __('admin.google_play_store'),
                        'active' => [env('ADMIN_URL') . '/google-scraper/*'],
                        'url' => env('ADMIN_URL') . '/google-scraper',
                    ],
                    [
                        'text' => __('admin.scraper_categories_google'),
                        'url' => env('ADMIN_URL') . '/scraper_categories_google',
                    ],
                    [
                        'text' => __('admin.apple_app_store'),
                        'active' => [env('ADMIN_URL') . '/apple-scraper/*'],
                        'url' => env('ADMIN_URL') . '/apple-scraper',
                    ],
                    [
                        'text' => __('admin.scraper_categories_apple'),
                        'url' => env('ADMIN_URL') . '/scraper_categories_apple',
                    ],
                ],
            ]);

            $event->menu->addAfter('content_manager', [
                'key' => 'ads',
                'text' => __('admin.ads'),
                'active' => [env('ADMIN_URL') . '/ads/*/edit'],
                'url' => env('ADMIN_URL') . '/ads',
                'icon' => 'fas fa-fw fa-flag mr-1',
            ]);

            $event->menu->addAfter('ads', [
                'key' => 'settings',
                'text' => __('admin.settings'),
                'icon' => 'fas fa-fw fa-cog mr-1',
                'submenu' => [
                    [
                        'text' => __('admin.general_settings'),
                        'url' => env('ADMIN_URL') . '/general_settings',
                    ],
                    [
                        'active' => [env('ADMIN_URL') . '/seo_settings/*'],
                        'text' => __('admin.seo_settings'),
                        'url' => env('ADMIN_URL') . '/seo_settings',
                    ],
                    [
                        'active' => [env('ADMIN_URL') . '/openai_settings/*'],
                        'text' => __('admin.openai_settings'),
                        'url' => env('ADMIN_URL') . '/openai_settings',
                    ],
                    [
                        'active' => [env('ADMIN_URL') . '/cdn_settings/*'],
                        'text' => __('admin.cdn_settings'),
                        'url' => env('ADMIN_URL') . '/cdn_settings',
                    ],
                    [
                        'text' => __('admin.sitemap_settings'),
                        'url' => env('ADMIN_URL') . '/sitemap_settings',
                    ],
                    [
                        'text' => __('admin.pwa_settings'),
                        'url' => env('ADMIN_URL') . '/pwa_settings',
                    ],
                    [
                    'active' => [env('ADMIN_URL') . '/error_handling/*'],
                        'text' => __('admin.error_handling'),
                        'url' => env('ADMIN_URL') . '/error_handling',
                    ],
                    [
                        'active' => [env('ADMIN_URL') . '/translations/*/edit', env('ADMIN_URL') . '/translations/create'],
                        'text' => __('admin.translations'),
                        'url' => env('ADMIN_URL') . '/translations',
                    ],
                ],
            ]);

            $pending_comments = Comment::where('approval', '0')->count('id');

            if ($pending_comments >= 1) {
                $event->menu->addAfter('settings', [
                    'key' => 'comments',
                    'text' => __('admin.comments'),
                    'url' => env('ADMIN_URL') . '/comments',
                    'icon' => 'fas fa-fw fa-comments mr-1',
                    'label' => $pending_comments,
                    'label_color' => 'warning',
                ]);
            } else {
                $event->menu->addAfter('settings', [
                    'key' => 'comments',
                    'text' => __('admin.comments'),
                    'url' => env('ADMIN_URL') . '/comments',
                    'icon' => 'fas fa-fw fa-comments mr-1',
                ]);
            }

            $pending_submissions = Submission::count('id');

            if ($pending_submissions >= 1) {
                $event->menu->addAfter('comments', [
                    'key' => 'submissions',
                    'text' => __('admin.submissions'),
                    'active' => [env('ADMIN_URL') . '/submissions/*'],
                    'url' => env('ADMIN_URL') . '/submissions',
                    'icon' => 'fas fa-fw fa-file-import mr-1',
                    'label' => $pending_submissions,
                    'label_color' => 'success',
                ]);
            } else {
                $event->menu->addAfter('comments', [
                    'key' => 'submissions',
                    'text' => __('admin.submissions'),
                    'url' => env('ADMIN_URL') . '/submissions',
                    'icon' => 'fas fa-fw fa-file-import mr-1',
                ]);
            }

            $pending_reports = Report::where('solved', '0')->count('id');

            if ($pending_reports >= 1) {
                $event->menu->addAfter('submissions', [
                    'key' => 'reports',
                    'text' => __('admin.reports'),
                    'url' => env('ADMIN_URL') . '/reports',
                    'icon' => 'fas fa-fw fa-exclamation-triangle mr-1',
                    'label' => $pending_reports,
                    'label_color' => 'danger',
                ]);
            } else {
                $event->menu->addAfter('submissions', [
                    'key' => 'reports',
                    'text' => __('admin.reports'),
                    'url' => env('ADMIN_URL') . '/reports',
                    'icon' => 'fas fa-fw fa-exclamation-triangle mr-1',
                ]);
            }

            $event->menu->addAfter('reports', [
                'key' => 'account_settings',
                'text' => __('admin.account_settings'),
                'url' => env('ADMIN_URL') . '/account_settings',
                'icon' => 'fas fa-fw fa-lock',
            ]);

            $event->menu->addAfter('account_settings', [
                'type' => 'link',
                'id' => 'clear_cache',
                'text' => __('admin.clear_cache'),
                'icon' => 'fas fa-fw fa-bolt',
                'url' => asset(env('ADMIN_URL') . '/general_settings/clear_cache'),
            ]);

            $event->menu->addBefore('app_ver', [
                'type' => 'link',
                'id' => 'documentation',
                'text' => __('admin.documentation'),
                'icon' => 'fas fa-fw fa-book',
                'target' => '_blank',
                'url' => 'https://rechain.ru',
            ]);

            $event->menu->addBefore('fullscreen-widget', [
                'key' => 'navbar_search',
                'type' => 'navbar-search',
                'text' => 'search',
                'topnav_right' => true,
            ]);

            $event->menu->addBefore('navbar_search', [
                'key' => 'browse_site',
                'type' => 'link',
                'text' => __('admin.browse_site'),
                'url' => '/',
                'target' => '_blank',
                'icon' => 'fas fa-external-link-alt mr-1',
                'topnav_right' => true,
            ]);

        });
    }
}