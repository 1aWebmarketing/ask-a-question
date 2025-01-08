=== Ask a Question ===
Contributors: Michael Homeister
Tags: survey, question
Requires at least: 5.3
Tested up to: 5.9
Stable tag: 1.0.0
Version: Trunk

Einfaches Umfrage-System

== Description ==

Nutze den Shortcode [question] um eine neue Frage zu stellen. Die Frage wird im Attribut question gestellt, die Antworten im Attribut answers

[question question="Was ist 3*2?" answers="4;6;12;19"]

* Antworten sind im Attribut answers durch Semikolon; getrennt
* bar_color="#rrggbb" Färbt die Progress-Bar in der gewünschten Farbe, wenn weggelassen dann ist die Farbe --color-main
* cookie="[1/0]" Setzt einen Cookie sobald abgestimmt wurde, so kann ein user nur einmal abstimmen
* show_results="[1/0]" Zeigt die Ergebnisse der Umfrage schon vor dem anklicken ja oder nein

== Changelog ==

= 1.1.0 =
* Prozentanzeige hinzugefügt

= 1.0.5 =
* Nichts gemacht

= 1.0.4 =
* Matomo Ereignis eingeführt "Ask-A-Question" "Vote" "Seitentitel"

= 1.0.3 =
* Umfrage zurücksetzen Option hinzugefügt
* Wenn man als Admin eingeloggt ist sieht auch ohne abstimmen zu müssen das Ergebnis

= 1.0.2 =
* Wenn Cookie aktiv ist wird ausgewählter Abstimmungs-Punkt markiert

= 1.0.1 =
* bar_color Attribut hinzugefügt

= 1.0.0 =
* Initial commit
