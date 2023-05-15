<?php

return [
    'tt_content' => [
        [
            'pid' => '1',
            'uid' => '1',
            'CType' => 'list',
            'list_type' => 'events_datelist',
            'header' => 'Kino Events',
            'pi_flexform' => '<?xml version="1.0" encoding="utf-8" standalone="yes" ?>
                <T3FlexForms>
                    <data>
                        <sheet index="sDEF">
                            <language index="lDEF">
                                <field index="settings.locations">
                                    <value index="vDEF">1,2</value>
                                </field>
                            </language>
                        </sheet>
                    </data>
                </T3FlexForms>
            ',
        ],
    ],
    'tx_events_domain_model_location' => [
        [
            'uid' => '1',
            'pid' => '2',
            'name' => 'Theater im Gewölbe',
            'street' => 'Cranach-Haus Markt 11/12',
            'city' => 'Weimar',
            'zip' => '99423',
            'country' => 'Deutschland',
            'longitude' => '11.330248',
            'latitude' => '50.979349',
        ],
        [
            'uid' => '2',
            'pid' => '2',
            'name' => 'Künstlergarten Weimar',
            'street' => 'Theaterplatz 4',
            'city' => 'Weimar',
            'zip' => '99423',
            'country' => 'Deutschland',
            'longitude' => '11.3262489',
            'latitude' => '50.9800023',
            'district' => 'Zentrum',
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
            'location' => '1',
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
            'location' => '2',
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
];
