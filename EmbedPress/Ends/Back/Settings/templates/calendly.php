<?php
/*
 * It will be customzed for OpenSea
 *  All undefined vars comes from 'render_settings_page' method
 *  */

$authorize_url = 'https://auth.calendly.com/oauth/authorize?client_id=RVIzKSKamm_V88B9Z7yB2fr4JBd7Bqbdi_VQ5rlji2I&response_type=code&redirect_uri=https://api.embedpress.com/calendly.php&state=' . admin_url('admin.php');


$user_info = !empty(get_option('calendly_user_info')) ? get_option('calendly_user_info') : [];
$event_types = !empty(get_option('calendly_event_types')) ? get_option('calendly_event_types') : [];
$scheduled_events = !empty(get_option('calendly_scheduled_events')) ? get_option('calendly_scheduled_events') : [];
$invtitees_list = !empty(get_option('calendly_invitees_list')) ? get_option('calendly_invitees_list') : [];

$avatarUrl = !empty($user_info['resource']['avatar_url']) ? $user_info['resource']['avatar_url'] : ' ';
$name = !empty($user_info['resource']['name']) ? $user_info['resource']['name'] : ' ';
$schedulingUrl = !empty($user_info['resource']['scheduling_url']) ? $user_info['resource']['scheduling_url'] : ' ';

if (!function_exists('getCalendlyUuid')) {
    function getCalendlyUuid($url)
    {
        $pattern = '/\/([0-9a-fA-F-]+)$/';
        if (preg_match($pattern, $url, $matches)) {
            $uuid = $matches[1];
            return $uuid;
        }
        return '';
    }
}

$is_calendly_connected = get_option('is_calendly_connected');


if(!is_embedpress_pro_active() || !$is_calendly_connected){
    $invtitees_list = [
        'e84408fc-d58a-421a-bf65-5efeefa182b0' => [
            'collection' => [
                0 => [
                    'name' => 'John Smith'
                ]
            ]
        ],
        'caf8a25a-4021-48ef-9322-2487b239bbef' => [
            'collection' => [
                0 => [
                    'name' => 'Emily Johnson'
                ]
            ]
        ],
        '9756459c-443e-4366-a147-98dc8b5aa09f' => [
            'collection' => [
                0 => [
                    'name' => 'Michael Davis'
                ]
            ]
        ],
        'ebc4b1fe-2d19-4079-bac9-1988588717f8' => [
            'collection' => [
                0 => [
                    'name' => 'Sarah Wilson'
                ]
            ]
        ],
        '232ab5df-50fb-4f16-a887-a00a2922c758' => [
            'collection' => [
                0 => [
                    'name' => 'David Brown'
                ]
            ]
        ],
    ];
    
    $scheduled_events = [
        'collection' => [
            [
                'uri' => 'https://api.calendly.com/scheduled_events/e84408fc-d58a-421a-bf65-5efeefa182b0',
                'name' => 'Daily Stand-up meeting',
                'start_time' => '2023-08-24T03:30:00.000000Z',
                'end_time' => '2023-08-24T03:45:00.000000Z',
                'status' => 'active',
            ],
            [
                'uri' => 'https://api.calendly.com/scheduled_events/caf8a25a-4021-48ef-9322-2487b239bbef',
                'name' => 'Daily Stand-up meeting',
                'start_time' => '2023-08-31T03:00:00.000000Z',
                'end_time' => '2023-08-31T03:15:00.000000Z',
                'status' => 'canceled',
            ],
            [
                'uri' => 'https://api.calendly.com/scheduled_events/9756459c-443e-4366-a147-98dc8b5aa09f',
                'name' => 'Daily Stand-up meeting',
                'start_time' => '2023-09-04T09:00:00.000000Z',
                'end_time' => '2023-09-04T09:15:00.000000Z',
                'status' => 'active',
            ],
            [
                'uri' => 'https://api.calendly.com/scheduled_events/ebc4b1fe-2d19-4079-bac9-1988588717f8',
                'name' => 'Daily Stand-up meeting',
                'start_time' => '2023-09-05T03:00:00.000000Z',
                'end_time' => '2023-09-05T03:15:00.000000Z',
                'status' => 'active',
            ],
            [
                'uri' => 'https://api.calendly.com/scheduled_events/232ab5df-50fb-4f16-a887-a00a2922c758',
                'name' => 'Town Hall Meeting',
                'start_time' => '2023-09-06T03:00:00.000000Z',
                'end_time' => '2023-09-06T03:15:00.000000Z',
                'status' => 'active',
            ],
        ]
    ];
    
    $event_types = [
        'collection' => [
            [
                'scheduling_url' => 'https://calendly.com/akash-mia/30min',
                'name' => '30 Minute Meeting',
                'active' => false,
            ],
            [
                'scheduling_url' => 'https://calendly.com/akash-mia/coffee-with-john-doe',
                'name' => 'Coffee with John Doe',
                'active' => true,
            ],
            [
                'scheduling_url' => 'https://calendly.com/akash-mia/asia-cup-2023',
                'name' => 'Asia Cup 2023',
                'active' => false,
            ],
            [
                'scheduling_url' => 'https://calendly.com/akash-mia/dailly-stand-up-meeting',
                'name' => 'Dailly Stand-up meeting',
                'active' => false,
            ],
            [
                'scheduling_url' => 'https://calendly.com/akash-mia/icc-mega-event',
                'name' => 'ICC Mega Event',
                'active' => false,
            ],
            [
                'scheduling_url' => 'https://calendly.com/akash-mia/wpdeveloper-team-meeting',
                'name' => 'WPDeveloper Team meeting',
                'active' => false,
            ],
        ]
    ];
}


$calendly_tokens = get_option('calendly_tokens');
$expirationTime = $calendly_tokens['created_at'] + $calendly_tokens['expires_in'];
$currentTimestamp = time();

?>

<div class="embedpress_calendly_settings  background__white radius-25 p40">
    <h3 class="calendly-settings-title"><?php esc_html_e("Calendly Settings", "embedpress"); ?></h3>
    <div class="calendly-embedpress-authorize-button">

        <div class="calendly-connector-container">
            <div class="account-wrap full-width-layout">

                <?php if (!empty($is_calendly_connected)) : ?>
                    <div title="<?php echo esc_attr__('Calendly already connected', 'embedpress'); ?>">
                        <a href="#" class="calendly-connect-button calendly-connected">
                            <img class="embedpress-calendly-icon" src="<?php echo EMBEDPRESS_SETTINGS_ASSETS_URL; ?>img/calendly.svg" alt="calendly">
                            <?php echo esc_html__('Connected', 'embedpress'); ?>
                        </a>
                    </div>
                <?php else : ?>
                    <a href="<?php echo esc_url($authorize_url); ?>" class="calendly-connect-button" target="_self" title="Connect with Calendly">
                        <img class="embedpress-calendly-icon" src="<?php echo EMBEDPRESS_SETTINGS_ASSETS_URL; ?>img/calendly.svg" alt="calendly">
                        <?php echo esc_html__('Connect with Calendly', 'embedpress'); ?>
                    </a>
                <?php endif; ?>
            </div>
            <?php if (is_array($scheduled_events) && count($scheduled_events) > 0) : ?>
                    <div class="calendly-sync-button">
                        <a href="<?php echo esc_url($authorize_url); ?>" class="calendly-connect-button" target="_self" title="Sync new calendly data">
                            <span class="dashicons dashicons-update-alt emcs-dashicon"></span><?php echo esc_html__('Sync', 'embedpress'); ?>
                        </a>
                    </div>
            <?php endif; ?>
        </div>
        <div class="tab-container">
            <div class="calendly-Event-button tab active-tab" onclick="showTab('event-types')">Event Types</div>
            <!-- Scheduled Events Tab -->
            <div class="calendly-Scheduled-button tab" onclick="showTab('scheduled-events')">Scheduled Events</div>
        </div>

    </div>



    <!-- Event Types Content -->
    <div class="tab-content active" id="event-types">
        <div class="event-type-list">
            <div class="event-type-group-list">
                <div class="event-type-group-list-item user-item">

                    <div class="list-header">
                        <?php if(!empty($avatarUrl)): ?>
                            <div class="calendly-profile-avatar">
                                <img src="<?php echo esc_url($avatarUrl); ?>" alt="<?php echo esc_attr($name); ?>">
                            </div>
                        <?php endif; ?>
                        <div class="calendly-user">
                            <div class="KF8rYwhNst0H6JyJ1_kq">
                                <span>
                                    <p style="color: currentcolor;"><?php echo esc_html($name); ?></p>
                                </span>
                            </div>
                            <a target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($schedulingUrl); ?>">
                                <span><?php echo esc_html($schedulingUrl); ?></span>
                            </a>
                        </div>
                    </div>
            <div class="calendly-data<?php if (!is_embedpress_pro_active()): echo '-placeholder'; endif; ?>">

                    <div class="event-type-card-list">
                        <?php
                            if (is_array($event_types) && count($event_types) > 0) :
                                foreach ($event_types['collection'] as $item) :
                                    $status = 'In-active';
                                    if (!empty($item['active'])) {
                                        $status = 'Active';
                                    }
                                    ?>
                                <div class="event-type-card-list-item" data-event-status="<?php echo esc_attr($status); ?>" style="color: var(--calendly-card-color); ">
                                    <div class="event-type-card">
                                        <div class="event-type-card-top">
                                            <h2><?php echo esc_html($item['name']); ?></h2>
                                            <p>30 mins, One-on-One</p>
                                            <a target="_blank" href="<?php echo esc_url($item['scheduling_url']); ?>"><?php echo esc_html__('View booking page', 'embedpress'); ?></a>
                                        </div>
                                        <div class="event-type-card-bottom">
                                            <div class="calendly-event-copy-link" data-event-link="<?php echo esc_url($item['scheduling_url']); ?>">
                                                <svg width="40" height="40" viewBox="0 0 0.75 0.75" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.05 0.476a0.076 0.076 0 0 0 0.076 0.074H0.2V0.5H0.126A0.026 0.026 0 0 1 0.1 0.474V0.124A0.026 0.026 0 0 1 0.126 0.098h0.35a0.026 0.026 0 0 1 0.026 0.026V0.2H0.276A0.076 0.076 0 0 0 0.2 0.276v0.35A0.076 0.076 0 0 0 0.276 0.7h0.35A0.076 0.076 0 0 0 0.702 0.624V0.274A0.076 0.076 0 0 0 0.626 0.2H0.55V0.126A0.076 0.076 0 0 0 0.476 0.05H0.126a0.076 0.076 0 0 0 -0.076 0.076v0.35Zm0.2 -0.2A0.026 0.026 0 0 1 0.276 0.25h0.35a0.026 0.026 0 0 1 0.026 0.026v0.35a0.026 0.026 0 0 1 -0.026 0.026H0.276A0.026 0.026 0 0 1 0.25 0.626V0.276Z" fill="#3664ae" /></svg>
                                                <span><?php echo esc_html__( 'Copy link', 'embedpress' ); ?></span>
                                            </div>
                                            <div class="event-status <?php echo esc_attr($status); ?>">
                                                <?php echo esc_html($status); ?>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                        <?php
                                endforeach;
                            endif;

                            ?>
                    </div>
                        
                    <?php if (!is_embedpress_pro_active()): ?>
                        <div class="overlay">
                            <a href="<?php echo esc_url('https://wpdeveloper.com/in/upgrade-embedpress'); ?>" class="overlay-button" target="_blank"><?php echo esc_html__('Get PRO to Unlock', 'embedpress'); ?></a>
                        </div>
                    <?php endif; ?>
            </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Scheduled Events Content -->
    <div class="tab-content" id="scheduled-events">

        <div class="calendly-day-list">
            <div class="calendly-data<?php if (!is_embedpress_pro_active()): echo '-placeholder'; endif; ?>">
                <table class="rwd-table" cellspacing="0">
                    <tbody>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Event</th>
                            <th>Scheduled Events</th>
                        </tr>
                        <?php
                            $index = 0;
                            $current_datetime = new DateTime(); // Get the current date and time

                            $upcoming_events = [];
                            $past_events = [];

                            if (is_array($scheduled_events) && count($scheduled_events) > 0) {
                                foreach ($scheduled_events['collection'] as $event) {
                                    $uuid = getCalendlyUuid($event['uri']);


                                    $name = $invtitees_list[$uuid]['collection'][$index]['name'];

                                    // Convert event start and end times to DateTime objects
                                    $start_time = new DateTime($event['start_time']);
                                    $end_time = new DateTime($event['end_time']);

                                    // Check if the event is in the past or upcoming
                                    $is_past_event = $end_time < $current_datetime;

                                    // Categorize events into upcoming and past
                                    if ($is_past_event) {
                                        $past_events[] = [
                                            'event' => $event,
                                            'name' => $name,
                                        ];
                                    } else {
                                        $upcoming_events[] = [
                                            'event' => $event,
                                            'name' => $name,
                                        ];
                                    }
                                }
                            }

                            // Sort upcoming events by start time
                            usort($upcoming_events, function ($a, $b) {
                                return strtotime($a['event']['start_time']) - strtotime($b['event']['start_time']);
                            });

                            // Sort past events by start time in descending order
                            usort($past_events, function ($a, $b) {
                                return strtotime($b['event']['start_time']) - strtotime($a['event']['start_time']);
                            });

                            // Merge upcoming and past events for display
                            $sorted_events = array_merge($upcoming_events, $past_events);


                            if (is_array($sorted_events) && count($sorted_events) > 0) :
                                foreach ($sorted_events as $event_data) :
                                    $event = $event_data['event'];
                                    $name = $event_data['name'];

                                    // Convert event start and end times to DateTime objects
                                    $start_time = new DateTime($event['start_time']);
                                    $end_time = new DateTime($event['end_time']);

                                    // Check if the event is in the past or upcoming
                                    $is_past_event = $end_time < $current_datetime;
                                    ?>

                                <tr>
                                    <td class="event-date"><?php echo esc_html(date('l, j F Y', strtotime($event['start_time']))); ?></td>
                                    <td class="event-time"><?php echo esc_html(date('h:ia', strtotime($event['start_time'])) . ' - ' . date('h:ia', strtotime($event['end_time']))); ?></td>
                                    <td class="event-info">
                                        <strong><?php echo esc_html($name); ?></strong><br>
                                        Event type: <strong><?php echo esc_html($event['name']); ?></strong>
                                    </td>
                                    <td class="event-action">
                                        <?php echo $is_past_event ? 'Past' : 'Upcoming'; ?>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        <?php endif; ?>

                    </tbody>
                </table>

                <?php if (!is_embedpress_pro_active()): ?>
                <div class="overlay">
                    <a href="<?php echo esc_url('https://wpdeveloper.com/in/upgrade-embedpress'); ?>" class="overlay-button" target="_blank"><?php echo esc_html__('Get PRO to Unlock', 'embedpress'); ?></a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // JavaScript function to switch between tabs
        function showTab(tabId) {
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');

            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.remove('active');
            });

            // Deactivate all tabs
            tabs.forEach(tab => {
                tab.classList.remove('active-tab');
            });

            // Activate the selected tab
            document.getElementById(tabId).classList.add('active');
            event.currentTarget.classList.add('active-tab');
        }
    </script>



</div>