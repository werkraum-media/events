<?php

declare(strict_types=1);

return [
    'tt_content' => [
        [
            'pid' => '1',
            'uid' => '1',
            'CType' => 'events_datelisttest',
            'header' => 'Kino Events',
            'pi_flexform' => '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
                <T3FlexForms>
                    <data>
                        <sheet index="sDEF">
                            <language index="lDEF">
                                <field index="settings.categories">
                                    <value index="vDEF">1</value>
                                </field>
                                <field index="settings.includeSubcategories">
                                    <value index="vDEF">1</value>
                                </field>
                                <field index="settings.categoryCombination">
                                    <value index="vDEF">1</value>
                                </field>
                            </language>
                        </sheet>
                    </data>
                </T3FlexForms>
            ',
        ],
    ],
    'tx_events_domain_model_event' => [
        [
            'uid' => '1',
            'pid' => '2',
            'title' => 'Was hat das Universum mit mir zu tun?',
            'global_id' => 'e_100478529',
            'teaser' => '„WAS HAT DAS UNIVERSUM MIT MIR ZU TUN?“
            Ein Abend mit Prof. Dr. Harald Lesch',
            'details' => '„WAS HAT DAS UNIVERSUM MIT MIR ZU TUN?“
            Ein Abend mit Prof. Dr. Harald Lesch
            Auf den Spuren von Goethes Naturphilosophie ist der Astrophysiker und Wissenschaftsjournalist Prof. Dr. Harald Lesch in Weimar schon mehrmals präsent gewesen. Jetzt hält er einen Vortrag zu keiner geringeren Frage als „Was hat das Universum mit mir zu tun?“ Ob Goethe darauf eine pointierte Antwort eingefallen wäre? Sein Faust wollte die Spur seiner Erdentage nicht in Äonen untergehen sehen. Harald Lesch behauptet: Wir sind und bleiben stets Teil der Äonen - denn „wir sind alle Sternenstaub. Vor einer halben Ewigkeit ist ein Stern explodiert und hat alle Stoffe aus denen wir bestehen hervorgebracht. Und wenn das bei uns geklappt hat, könnte es auch noch woanders passiert sein.“ Erleben Sie einen faszinierenden Mix aus Rednerkunst und virtuoser musikalischer Begleitung. Neben Prof. Dr. Harald Lesch begibt sich der Musiker Hans Raths (Bayon) mit auf die Reise ins theatralische und philosophische Universum. Eine Veranstaltung nicht nur für Science-Fiction-Freaks, sondern für alle Kosmopoliten!',
            'price_info' => 'Preis inklusive Platzierung mit Namensschild und einem Pausengetränk Ihrer Wahl',
            'categories' => '1',
        ],
        [
            'uid' => '2',
            'pid' => '2',
            'title' => 'Lotte in Weimar',
            'global_id' => 'e_100453137',
            'teaser' => 'Ein „Goethe-Götter-Lustspiel“ nach dem gleichnamigen Roman von Thomas Mann',
            'details' => 'LOTTE IN WEIMAR
            Ein „Goethe-Götter-Lustspiel“ nach dem gleichnamigen Roman von Thomas Mann
            „Welch buchenswertes Ereignis!“, ruft der Kellner Mager aus, als er erfährt, wer da in seinem Gasthaus „Zum Elephanten“ abgestiegen ist: Die berühmte Heldin aus Goethes „Die Leiden des jungen Werthers“, Charlotte Kestner, geborene Buff aus Wetzlar, – das „Urbild“ der Lotte sozusagen! Eine heiter-ironische Abrechnung mit dem Starkult anno 1816 fast am Originalschauplatz. Mit Regine Heintze, Heike Meyer und Detlef Heintze. Inszenierung: Michael Kliefert/ Detlef Heintze.',
            'price_info' => 'Preise inklusive Platzierung mit Namensschild und einem Pausengetränk Ihrer Wahl (ermäßigt alkoholfrei)',
            'categories' => '1',
        ],
    ],
    'tx_events_domain_model_date' => [
        [
            'uid' => '1',
            'pid' => '2',
            'event' => '1',
            'start' => '1661626800',
            'end' => '1661632200',
        ],
        [
            'uid' => '2',
            'pid' => '2',
            'event' => '1',
            'start' => '1660158000',
            'end' => '1660163400',
        ],
        [
            'uid' => '3',
            'pid' => '2',
            'event' => '2',
            'start' => '1661194800',
            'end' => '1661200200',
        ],
    ],
    'sys_category' => [
        [
            'uid' => '1',
            'pid' => '2',
            'parent' => '0',
            'title' => 'Events',
        ],
        [
            'uid' => '2',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - Highlight',
        ],
        [
            'uid' => '3',
            'pid' => '2',
            'parent' => '2',
            'title' => 'Events - Highlight - New',
        ],
        [
            'uid' => '4',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - Tours',
        ],
        [
            'uid' => '5',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - For Groups',
        ],
        [
            'uid' => '6',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - For Individuals',
        ],
        [
            'uid' => '7',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - For Families',
        ],
        [
            'uid' => '8',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - For Children',
        ],
        [
            'uid' => '9',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - Speak',
        ],
        [
            'uid' => '10',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - Dinner',
        ],
        [
            'uid' => '11',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - Some more',
        ],
        [
            'uid' => '12',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - Some more',
        ],
        [
            'uid' => '13',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - Some more',
        ],
        [
            'uid' => '14',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - Some more',
        ],
        [
            'uid' => '15',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - Some more',
        ],
        [
            'uid' => '16',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - Some more',
        ],
        [
            'uid' => '17',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - Some more',
        ],
        [
            'uid' => '18',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - Some more',
        ],
        [
            'uid' => '19',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - Some more',
        ],
        [
            'uid' => '20',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - Some more',
        ],
        [
            'uid' => '21',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - Some more',
        ],
        [
            'uid' => '22',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - Some more',
        ],
        [
            'uid' => '23',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - Some more',
        ],
        [
            'uid' => '24',
            'pid' => '2',
            'parent' => '1',
            'title' => 'Events - Some more',
        ],
    ],
    'sys_category_record_mm' => [
        [
            'uid_local' => '5',
            'uid_foreign' => '1',
            'tablenames' => 'tx_events_domain_model_event',
            'fieldname' => 'categories',
            'sorting' => '2',
            'sorting_foreign' => '1',
        ],
        [
            'uid_local' => '3',
            'uid_foreign' => '2',
            'tablenames' => 'tx_events_domain_model_event',
            'fieldname' => 'categories',
            'sorting' => '2',
            'sorting_foreign' => '1',
        ],
    ],
];
