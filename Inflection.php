<?php

namespace futuretek\shared;

/**
 * Class Inflection
 *
 * @package futuretek\shared
 * @author  Lukas Cerny <lukas.cerny@futuretek.cz> based on work of Pavel Sedlak (Pteryx)
 * @license Apache-2.0
 * @link    http://www.futuretek.cz
 */
class Inflection
{
    private $_v1i;
    protected static $instance;

    public function __construct()
    {
        //  Databaze vzoru pro sklonovani
        $this->vzor = [];
        $nvz = 0;
        $this->isDbgMode = false;

        // Přídavná jména a zájmena
        $this->vzor[$nvz++] = ['m', '-ký', 'kého', 'kému', 'ký/kého', 'ký', 'kém', 'kým', '-ké/-cí', 'kých', 'kým', 'ké', '-ké/-cí', 'kých', 'kými'];
        $this->vzor[$nvz++] = ['m', '-rý', 'rého', 'rému', 'rý/rého', 'rý', 'rém', 'rým', '-ré/-ří', 'rých', 'rým', 'ré', '-ré/-ří', 'rých', 'rými'];
        $this->vzor[$nvz++] = ['m', '-chý', 'chého', 'chému', 'chý/chého', 'chý', 'chém', 'chým', '-ché/-ší', 'chých', 'chým', 'ché', '-ché/-ší', 'chých', 'chými'];
        $this->vzor[$nvz++] = ['m', '-hý', 'hého', 'hému', 'hý/hého', 'hý', 'hém', 'hým', '-hé/-zí', 'hých', 'hým', 'hé', '-hé/-zí', 'hých', 'hými'];
        $this->vzor[$nvz++] = ['m', '-ý', 'ého', 'ému', 'ý/ého', 'ý', 'ém', 'ým', '-é/-í', 'ých', 'ým', 'é', '-é/-í', 'ých', 'ými'];
        $this->vzor[$nvz++] = ['m', '-[aeěií]cí', '0cího', '0címu', '0cí/0cího', '0cí', '0cím', '0cím', '0cí', '0cích', '0cím', '0cí', '0cí', '0cích', '0cími'];
        $this->vzor[$nvz++] = ['ž', '-[aeěií]cí', '0cí', '0cí', '0cí', '0cí', '0cí', '0cí', '0cí', '0cích', '0cím', '0cí', '0cí', '0cích', '0cími'];
        $this->vzor[$nvz++] = ['s', '-[aeěií]cí', '0cího', '0címu', '0cí/0cího', '0cí', '0cím', '0cím', '0cí', '0cích', '0cím', '0cí', '0cí', '0cích', '0cími'];
        $this->vzor[$nvz++] = ['m', '-[bcčdhklmnprsštvzž]ní', '0ního', '0nímu', '0ní/0ního', '0ní', '0ním', '0ním', '0ní', '0ních', '0ním', '0ní', '0ní', '0ních', '0ními'];
        $this->vzor[$nvz++] = ['ž', '-[bcčdhklmnprsštvzž]ní', '0ní', '0ní', '0ní', '0ní', '0ní', '0ní', '0ní', '0ních', '0ním', '0ní', '0ní', '0ních', '0ními'];
        $this->vzor[$nvz++] = ['s', '-[bcčdhklmnprsštvzž]ní', '0ního', '0nímu', '0ní/0ního', '0ní', '0ním', '0ním', '0ní', '0ních', '0ním', '0ní', '0ní', '0ních', '0ními'];

        $this->vzor[$nvz++] = ['m', '-[i]tel', '0tele', '0teli', '0tele', '0tel', '0teli', '0telem', '0telé', '0telů', '0telům', '0tele', '0telé', '0telích', '0teli'];
        $this->vzor[$nvz++] = ['m', '-[í]tel', '0tele', '0teli', '0tele', '0tel', '0teli', '0telem', 'átelé', 'áteli', 'átelům', 'átele', 'átelé', 'átelích', 'áteli'];

        $this->vzor[$nvz++] = ['s', '-é', 'ého', 'ému', 'é', 'é', 'ém', 'ým', '-á', 'ých', 'ým', 'á', 'á', 'ých', 'ými'];
        $this->vzor[$nvz++] = ['ž', '-á', 'é', 'é', 'ou', 'á', 'é', 'ou', '-é', 'ých', 'ým', 'é', 'é', 'ých', 'ými'];
        $this->vzor[$nvz++] = ['-', 'já', 'mne', 'mně', 'mne/mě', 'já', 'mně', 'mnou', 'my', 'nás', 'nám', 'nás', 'my', 'nás', 'námi'];
        $this->vzor[$nvz++] = ['-', 'ty', 'tebe', 'tobě', 'tě/tebe', 'ty', 'tobě', 'tebou', 'vy', 'vás', 'vám', 'vás', 'vy', 'vás', 'vámi'];
        $this->vzor[$nvz++] = ['-', 'my', '', '', '', '', '', '', 'my', 'nás', 'nám', 'nás', 'my', 'nás', 'námi'];
        $this->vzor[$nvz++] = ['-', 'vy', '', '', '', '', '', '', 'vy', 'vás', 'vám', 'vás', 'vy', 'vás', 'vámi'];
        $this->vzor[$nvz++] = ['m', 'on', 'něho', 'mu/jemu/němu', 'ho/jej', 'on', 'něm', 'ním', 'oni', 'nich', 'nim', 'je', 'oni', 'nich', 'jimi/nimi'];
        $this->vzor[$nvz++] = ['m', 'oni', '', '', '', '', '', '', 'oni', 'nich', 'nim', 'je', 'oni', 'nich', 'jimi/nimi'];
        $this->vzor[$nvz++] = ['ž', 'ony', '', '', '', '', '', '', 'ony', 'nich', 'nim', 'je', 'ony', 'nich', 'jimi/nimi'];
        $this->vzor[$nvz++] = ['s', 'ono', 'něho', 'mu/jemu/němu', 'ho/jej', 'ono', 'něm', 'ním', 'ona', 'nich', 'nim', 'je', 'ony', 'nich', 'jimi/nimi'];
        $this->vzor[$nvz++] = ['ž', 'ona', 'ní', 'ní', 'ji', 'ona', 'ní', 'ní', 'ony', 'nich', 'nim', 'je', 'ony', 'nich', 'jimi/nimi'];
        $this->vzor[$nvz++] = ['m', 'ten', 'toho', 'tomu', 'toho', 'ten', 'tom', 'tím', 'ti', 'těch', 'těm', 'ty', 'ti', 'těch', 'těmi'];
        $this->vzor[$nvz++] = ['ž', 'ta', 'té', 'té', 'tu', 'ta', 'té', 'tou', 'ty', 'těch', 'těm', 'ty', 'ty', 'těch', 'těmi'];
        $this->vzor[$nvz++] = ['s', 'to', 'toho', 'tomu', 'toho', 'to', 'tom', 'tím', 'ta', 'těch', 'těm', 'ta', 'ta', 'těch', 'těmi'];

        // přivlastňovací zájmena
        $this->vzor[$nvz++] = ['m', 'můj', 'mého', 'mému', 'mého', 'můj', 'mém', 'mým', 'mí', 'mých', 'mým', 'mé', 'mí', 'mých', 'mými'];
        $this->vzor[$nvz++] = ['ž', 'má', 'mé', 'mé', 'mou', 'má', 'mé', 'mou', 'mé', 'mých', 'mým', 'mé', 'mé', 'mých', 'mými'];
        $this->vzor[$nvz++] = ['ž', 'moje', 'mé', 'mé', 'mou', 'má', 'mé', 'mou', 'moje', 'mých', 'mým', 'mé', 'mé', 'mých', 'mými'];
        $this->vzor[$nvz++] = ['s', 'mé', 'mého', 'mému', 'mé', 'moje', 'mém', 'mým', 'mé', 'mých', 'mým', 'má', 'má', 'mých', 'mými'];
        $this->vzor[$nvz++] = ['s', 'moje', 'mého', 'mému', 'moje', 'moje', 'mém', 'mým', 'moje', 'mých', 'mým', 'má', 'má', 'mých', 'mými'];

        $this->vzor[$nvz++] = ['m', 'tvůj', 'tvého', 'tvému', 'tvého', 'tvůj', 'tvém', 'tvým', 'tví', 'tvých', 'tvým', 'tvé', 'tví', 'tvých', 'tvými'];
        $this->vzor[$nvz++] = ['ž', 'tvá', 'tvé', 'tvé', 'tvou', 'tvá', 'tvé', 'tvou', 'tvé', 'tvých', 'tvým', 'tvé', 'tvé', 'tvých', 'tvými'];
        $this->vzor[$nvz++] = ['ž', 'tvoje', 'tvé', 'tvé', 'tvou', 'tvá', 'tvé', 'tvou', 'tvé', 'tvých', 'tvým', 'tvé', 'tvé', 'tvých', 'tvými'];
        $this->vzor[$nvz++] = ['s', 'tvé', 'tvého', 'tvému', 'tvého', 'tvůj', 'tvém', 'tvým', 'tvá', 'tvých', 'tvým', 'tvé', 'tvá', 'tvých', 'tvými'];
        $this->vzor[$nvz++] = ['s', 'tvoje', 'tvého', 'tvému', 'tvého', 'tvůj', 'tvém', 'tvým', 'tvá', 'tvých', 'tvým', 'tvé', 'tvá', 'tvých', 'tvými'];

        $this->vzor[$nvz++] = ['m', 'náš', 'našeho', 'našemu', 'našeho', 'náš', 'našem', 'našim', 'naši', 'našich', 'našim', 'naše', 'naši', 'našich', 'našimi'];
        $this->vzor[$nvz++] = ['ž', 'naše', 'naší', 'naší', 'naši', 'naše', 'naší', 'naší', 'naše', 'našich', 'našim', 'naše', 'naše', 'našich', 'našimi'];
        $this->vzor[$nvz++] = ['s', 'naše', 'našeho', 'našemu', 'našeho', 'naše', 'našem', 'našim', 'naše', 'našich', 'našim', 'naše', 'naše', 'našich', 'našimi'];

        $this->vzor[$nvz++] = ['m', 'váš', 'vašeho', 'vašemu', 'vašeho', 'váš', 'vašem', 'vašim', 'vaši', 'vašich', 'vašim', 'vaše', 'vaši', 'vašich', 'vašimi'];
        $this->vzor[$nvz++] = ['ž', 'vaše', 'vaší', 'vaší', 'vaši', 'vaše', 'vaší', 'vaší', 'vaše', 'vašich', 'vašim', 'vaše', 'vaše', 'vašich', 'vašimi'];
        $this->vzor[$nvz++] = ['s', 'vaše', 'vašeho', 'vašemu', 'vašeho', 'vaše', 'vašem', 'vašim', 'vaše', 'vašich', 'vašim', 'vaše', 'vaše', 'vašich', 'vašimi'];

        $this->vzor[$nvz++] = ['m', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho'];
        $this->vzor[$nvz++] = ['ž', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho'];
        $this->vzor[$nvz++] = ['s', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho', 'jeho'];

        $this->vzor[$nvz++] = ['m', 'její', 'jejího', 'jejímu', 'jejího', 'její', 'jejím', 'jejím', 'její', 'jejích', 'jejím', 'její', 'její', 'jejích', 'jejími'];
        $this->vzor[$nvz++] = ['s', 'její', 'jejího', 'jejímu', 'jejího', 'její', 'jejím', 'jejím', 'její', 'jejích', 'jejím', 'její', 'její', 'jejích', 'jejími'];
        $this->vzor[$nvz++] = ['ž', 'její', 'její', 'její', 'její', 'její', 'její', 'její', 'její', 'jejích', 'jejím', 'její', 'její', 'jejích', 'jejími'];

        $this->vzor[$nvz++] = ['m', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich'];
        $this->vzor[$nvz++] = ['s', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich'];
        $this->vzor[$nvz++] = ['ž', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich', 'jejich'];

        // výjimky (zvl. běžná slova)
        $this->vzor[$nvz++] = ['m', '-bůh', 'boha', 'bohu', 'boha', 'bože', 'bohovi', 'bohem', 'bozi/bohové', 'bohů', 'bohům', 'bohy', 'bozi/bohové', 'bozích', 'bohy'];
        $this->vzor[$nvz++] = ['m', '-pan', 'pana', 'panu', 'pana', 'pane', 'panu', 'panem', 'páni/pánové', 'pánů', 'pánům', 'pány', 'páni/pánové', 'pánech', 'pány'];
        $this->vzor[$nvz++] = ['s', 'moře', 'moře', 'moři', 'moře', 'moře', 'moři', 'mořem', 'moře', 'moří', 'mořím', 'moře', 'moře', 'mořích', 'moři'];
        $this->vzor[$nvz++] = ['-', 'dveře', '', '', '', '', '', '', 'dveře', 'dveří', 'dveřím', 'dveře', 'dveře', 'dveřích', 'dveřmi'];
        $this->vzor[$nvz++] = ['-', 'housle', '', '', '', '', '', '', 'housle', 'houslí', 'houslím', 'housle', 'housle', 'houslích', 'houslemi'];
        $this->vzor[$nvz++] = ['-', 'šle', '', '', '', '', '', '', 'šle', 'šlí', 'šlím', 'šle', 'šle', 'šlích', 'šlemi'];
        $this->vzor[$nvz++] = ['-', 'muka', '', '', '', '', '', '', 'muka', 'muk', 'mukám', 'muka', 'muka', 'mukách', 'mukami'];
        $this->vzor[$nvz++] = ['s', 'ovoce', 'ovoce', 'ovoci', 'ovoce', 'ovoce', 'ovoci', 'ovocem', '', '', '', '', '', '', ''];
        $this->vzor[$nvz++] = ['m', 'humus', 'humusu', 'humusu', 'humus', 'humuse', 'humusu', 'humusem', 'humusy', 'humusů', 'humusům', 'humusy', 'humusy', 'humusech', 'humusy'];
        $this->vzor[$nvz++] = ['m', '-vztek', 'vzteku', 'vzteku', 'vztek', 'vzteku', 'vzteku', 'vztekem', 'vzteky', 'vzteků', 'vztekům', 'vzteky', 'vzteky', 'vztecích', 'vzteky'];
        $this->vzor[$nvz++] = ['m', '-dotek', 'doteku', 'doteku', 'dotek', 'doteku', 'doteku', 'dotekem', 'doteky', 'doteků', 'dotekům', 'doteky', 'doteky', 'dotecích', 'doteky'];
        $this->vzor[$nvz++] = ['ž', '-hra', 'hry', 'hře', 'hru', 'hro', 'hře', 'hrou', 'hry', 'her', 'hrám', 'hry', 'hry', 'hrách', 'hrami'];
        $this->vzor[$nvz++] = ['m', 'Zeus', 'Dia', 'Diovi', 'Dia', 'Die', 'Diovi', 'Diem', 'Diové', 'Diů', 'Diům', '?', 'Diové', '?', '?'];
        $this->vzor[$nvz++] = ['m', 'leden', 'ledna', 'lednu', 'leden', 'ledne', 'lednu', 'lednem', 'ledny', 'lednů', 'lednům', 'ledny', 'ledny', 'lednech', 'ledny'];
        $this->vzor[$nvz++] = ['m', 'únor', 'února', 'únoru', 'únor', 'únore', 'únoru', 'únorem', 'únory', 'únorů', 'únorům', 'únory', 'únory', 'únorech', 'únory'];
        $this->vzor[$nvz++] = ['m', 'březen', 'března', 'březnu', 'březen', 'březne', 'březnu', 'březnem', 'březny', 'březnů', 'březnům', 'březny', 'březny', 'březnech', 'březny'];
        $this->vzor[$nvz++] = ['m', 'duben', 'dubna', 'dubnu', 'duben', 'dubne', 'dubnu', 'dubnem', 'dubny', 'dubnů', 'dubnům', 'dubny', 'dubny', 'dubnech', 'dubny'];
        $this->vzor[$nvz++] = ['m', 'květen', 'května', 'květnu', 'květen', 'květne', 'květnu', 'květnem', 'květny', 'květnů', 'květnům', 'květny', 'květny', 'květnech', 'květny'];
        $this->vzor[$nvz++] = ['m', 'červen', 'června', 'červnu', 'červen', 'červne', 'červnu', 'červnem', 'červny', 'červnů', 'červnům', 'červny', 'červny', 'červnech', 'červny'];
        $this->vzor[$nvz++] = ['m', 'srpen', 'srpna', 'srpnu', 'srpen', 'srpne', 'srpnu', 'srpnem', 'srpny', 'srpnů', 'srpnům', 'srpny', 'srpny', 'srpnech', 'srpny'];
        $this->vzor[$nvz++] = ['m', 'říjen', 'října', 'říjnu', 'říjen', 'říjne', 'říjnu', 'říjnem', 'říjny', 'říjnů', 'říjnům', 'říjny', 'říjny', 'říjnech', 'říjny'];

        // číslovky
        $this->vzor[$nvz++] = ['-', '-tdva', 'tidvou', 'tidvoum', 'tdva', 'tdva', 'tidvou', 'tidvěmi', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-tdvě', 'tidvou', 'tidvěma', 'tdva', 'tdva', 'tidvou', 'tidvěmi', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-ttři', 'titří', 'titřem', 'ttři', 'ttři', 'titřech', 'titřemi', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-tčtyři', 'tičtyřech', 'tičtyřem', 'tčtyři', 'tčtyři', 'tičtyřech', 'tičtyřmi', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-tpět', 'tipěti', 'tipěti', 'tpět', 'tpět', 'tipěti', 'tipěti', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-tšest', 'tišesti', 'tišesti', 'tšest', 'tšest', 'tišesti', 'tišesti', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-tsedm', 'tisedmi', 'tisedmi', 'tsedm', 'tsedm', 'tisedmi', 'tisedmi', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-tosm', 'tiosmi', 'tiosmi', 'tosm', 'tosm', 'tiosmi', 'tiosmi', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-tdevět', 'tidevíti', 'tidevíti', 'tdevět', 'tdevět', 'tidevíti', 'tidevíti', '?', '?', '?', '?', '?', '?', '?'];

        $this->vzor[$nvz++] = ['ž', '-jedna', 'jedné', 'jedné', 'jednu', 'jedno', 'jedné', 'jednou', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['m', '-jeden', 'jednoho', 'jednomu', 'jednoho', 'jeden', 'jednom', 'jedním', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['s', '-jedno', 'jednoho', 'jednomu', 'jednoho', 'jedno', 'jednom', 'jedním', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-dva', 'dvou', 'dvoum', 'dva', 'dva', 'dvou', 'dvěmi', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-dvě', 'dvou', 'dvoum', 'dva', 'dva', 'dvou', 'dvěmi', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-tři', 'tří', 'třem', 'tři', 'tři', 'třech', 'třemi', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-čtyři', 'čtyřech', 'čtyřem', 'čtyři', 'čtyři', 'čtyřech', 'čtyřmi', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-pět', 'pěti', 'pěti', 'pět', 'pět', 'pěti', 'pěti', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-šest', 'šesti', 'šesti', 'šest', 'šest', 'šesti', 'šesti', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-sedm', 'sedmi', 'sedmi', 'sedm', 'sedm', 'sedmi', 'sedmi', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-osm', 'osmi', 'osmi', 'osm', 'osm', 'osmi', 'osmi', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-devět', 'devíti', 'devíti', 'devět', 'devět', 'devíti', 'devíti', '?', '?', '?', '?', '?', '?', '?'];

        $this->vzor[$nvz++] = ['-', 'deset', 'deseti', 'deseti', 'deset', 'deset', 'deseti', 'deseti', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-ná[cs]t', 'ná0ti', 'ná0ti', 'ná0t', 'náct', 'ná0ti', 'ná0ti', '?', '?', '?', '?', '?', '?', '?'];

        $this->vzor[$nvz++] = ['-', '-dvacet', 'dvaceti', 'dvaceti', 'dvacet', 'dvacet', 'dvaceti', 'dvaceti', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-třicet', 'třiceti', 'třiceti', 'třicet', 'třicet', 'třiceti', 'třiceti', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-čtyřicet', 'čtyřiceti', 'čtyřiceti', 'čtyřicet', 'čtyřicet', 'čtyřiceti', 'čtyřiceti', '?', '?', '?', '?', '?', '?', '?'];
        $this->vzor[$nvz++] = ['-', '-desát', 'desáti', 'desáti', 'desát', 'desát', 'desáti', 'desáti', '?', '?', '?', '?', '?', '?', '?'];

        // Spec. přídady skloňování(+předseda, srdce jako úplná výjimka)
        $this->vzor[$nvz++] = ['m', '-[i]sta', '0sty', '0stovi', '0stu', '0sto', '0stovi', '0stou', '-0sté', '0stů', '0stům', '0sty', '0sté', '0stech', '0sty'];
        $this->vzor[$nvz++] = ['m', '-[o]sta', '0sty', '0stovi', '0stu', '0sto', '0stovi', '0stou', '-0stové', '0stů', '0stům', '0sty', '0sté', '0stech', '0sty'];
        $this->vzor[$nvz++] = ['m', '-předseda', 'předsedy', 'předsedovi', 'předsedu', 'předsedo', 'předsedovi', 'předsedou', 'předsedové', 'předsedů', 'předsedům', 'předsedy', 'předsedové', 'předsedech', 'předsedy'];
        $this->vzor[$nvz++] = ['m', '-srdce', 'srdce', 'srdi', 'sdrce', 'srdce', 'srdci', 'srdcem', 'srdce', 'srdcí', 'srdcím', 'srdce', 'srdce', 'srdcích', 'srdcemi'];
        $this->vzor[$nvz++] = ['m', '-[db]ce', '0ce', '0ci', '0ce', '0če', '0ci', '0cem', '0ci/0cové', '0ců', '0cům', '0ce', '0ci/0cové', '0cích', '0ci'];
        $this->vzor[$nvz++] = ['m', '-[jň]ev', '0evu', '0evu', '0ev', '0eve', '0evu', '0evem', '-0evy', '0evů', '0evům', '0evy', '0evy', '0evech', '0evy'];
        $this->vzor[$nvz++] = ['m', '-[lř]ev', '0evu/0va', '0evu/0vovi', '0ev/0va', '0eve/0ve', '0evu/0vovi', '0evem/0vem', '-0evy/0vové', '0evů/0vů', '0evům/0vům', '0evy/0vy', '0evy/0vové', '0evech/0vech', '0evy/0vy'];
        $this->vzor[$nvz++] = ['m', '-ů[lz]', 'o0u/o0a', 'o0u/o0ovi', 'ů0/o0a', 'o0e', 'o0u', 'o0em', 'o-0y/o-0ové', 'o0ů', 'o0ům', 'o0y', 'o0y/o0ové', 'o0ech', 'o0y'];

        // výj. nůž ($this->vzor muž)
        $this->vzor[$nvz++] = ['m', 'nůž', 'nože', 'noži', 'nůž', 'noži', 'noži', 'nožem', 'nože', 'nožů', 'nožům', 'nože', 'nože', 'nožích', 'noži'];

        // vzor kolo
        $this->vzor[$nvz++] = ['s', '-[bcčdghksštvzž]lo', '0la', '0lu', '0lo', '0lo', '0lu', '0lem', '-0la', '0el', '0lům', '0la', '0la', '0lech', '0ly'];
        $this->vzor[$nvz++] = ['s', '-[bcčdnsštvzž]ko', '0ka', '0ku', '0ko', '0ko', '0ku', '0kem', '-0ka', '0ek', '0kům', '0ka', '0ka', '0cích/0kách', '0ky'];
        $this->vzor[$nvz++] = ['s', '-[bcčdksštvzž]no', '0na', '0nu', '0no', '0no', '0nu', '0nem', '-0na', '0en', '0nům', '0na', '0na', '0nech/0nách', '0ny'];
        $this->vzor[$nvz++] = ['s', '-o', 'a', 'u', 'o', 'o', 'u', 'em', '-a', '', 'ům', 'a', 'a', 'ech', 'y'];

        // vzor stavení
        $this->vzor[$nvz++] = ['s', '-í', 'í', 'í', 'í', 'í', 'í', 'ím', '-í', 'í', 'ím', 'í', 'í', 'ích', 'ími'];

        // vzor děvče  (če,dě,tě,ně,pě) výj.-také sele
        $this->vzor[$nvz++] = ['s', '-[čďť][e]', '10te', '10ti', '10', '10', '10ti', '10tem', '1-ata', '1at', '1atům', '1ata', '1ata', '1atech', '1aty'];
        $this->vzor[$nvz++] = ['s', '-[pb][ě]', '10te', '10ti', '10', '10', '10ti', '10tem', '1-ata', '1at', '1atům', '1ata', '1ata', '1atech', '1aty'];

        // vzor žena
        $this->vzor[$nvz++] = ['ž', '-[aeiouyáéíóúý]ka', '0ky', '0ce', '0ku', '0ko', '0ce', '0kou', '-0ky', '0k', '0kám', '0ky', '0ky', '0kách', '0kami'];
        $this->vzor[$nvz++] = ['ž', '-ka', 'ky', 'ce', 'ku', 'ko', 'ce', 'kou', '-ky', 'ek', 'kám', 'ky', 'ky', 'kách', 'kami'];
        $this->vzor[$nvz++] = ['ž', '-[bdghkmnptvz]ra', '0ry', '0ře', '0ru', '0ro', '0ře', '0rou', '-0ry', '0er', '0rám', '0ry', '0ry', '0rách', '0rami'];
        $this->vzor[$nvz++] = ['ž', '-ra', 'ry', 'ře', 'ru', 'ro', 'ře', 'rou', '-ry', 'r', 'rám', 'ry', 'ry', 'rách', 'rami'];
        $this->vzor[$nvz++] = ['ž', '-[tdbnvmp]a', '0y', '0ě', '0u', '0o', '0ě', '0ou', '-0y', '0', '0ám', '0y', '0y', '0ách', '0ami'];
        $this->vzor[$nvz++] = ['ž', '-cha', 'chy', 'še', 'chu', 'cho', 'še', 'chou', '-chy', 'ch', 'chám', 'chy', 'chy', 'chách', 'chami'];
        $this->vzor[$nvz++] = ['ž', '-[gh]a', '0y', 'ze', '0u', '0o', 'ze', '0ou', '-0y', '0', '0ám', '0y', '0y', '0ách', '0ami'];
        $this->vzor[$nvz++] = ['ž', '-ňa', 'ni', 'ně', 'ňou', 'ňo', 'ni', 'ňou', '-ně/ničky', 'ň', 'ňám', 'ně/ničky', 'ně/ničky', 'ňách', 'ňami'];
        $this->vzor[$nvz++] = ['ž', '-[šč]a', '0i', '0e', '0u', '0o', '0e', '0ou', '-0e/0i', '0', '0ám', '0e/0i', '0e/0i', '0ách', '0ami'];
        $this->vzor[$nvz++] = ['ž', '-a', 'y', 'e', 'u', 'o', 'e', 'ou', '-y', '', 'ám', 'y', 'y', 'ách', 'ami'];

        // vz. píseň
        $this->vzor[$nvz++] = ['ž', '-eň', 'ně', 'ni', 'eň', 'ni', 'ni', 'ní', '-ně', 'ní', 'ním', 'ně', 'ně', 'ních', 'němi'];
        $this->vzor[$nvz++] = ['ž', '-oň', 'oně', 'oni', 'oň', 'oni', 'oni', 'oní', '-oně', 'oní', 'oním', 'oně', 'oně', 'oních', 'oněmi'];
        $this->vzor[$nvz++] = ['ž', '-[ě]j', '0je', '0ji', '0j', '0ji', '0ji', '0jí', '-0je', '0jí', '0jím', '0je', '0je', '0jích', '0jemi'];

        // vzor růže
        $this->vzor[$nvz++] = ['ž', '-ev', 've', 'vi', 'ev', 'vi', 'vi', 'ví', '-ve', 'ví', 'vím', 've', 've', 'vích', 'vemi'];
        $this->vzor[$nvz++] = ['ž', '-ice', 'ice', 'ici', 'ici', 'ice', 'ici', 'icí', '-ice', 'ic', 'icím', 'ice', 'ice', 'icích', 'icemi'];
        $this->vzor[$nvz++] = ['ž', '-e', 'e', 'i', 'i', 'e', 'i', 'í', '-e', 'í', 'ím', 'e', 'e', 'ích', 'emi'];

        // vzor píseň
        $this->vzor[$nvz++] = ['ž', '-[eaá][jžň]', '10e/10i', '10i', '10', '10i', '10i', '10í', '-10e/10i', '10í', '10ím', '10e', '10e', '10ích', '10emi'];
        $this->vzor[$nvz++] = ['ž', '-[eayo][š]', '10e/10i', '10i', '10', '10i', '10i', '10í', '10e/10i', '10í', '10ím', '10e', '10e', '10ích', '10emi'];
        $this->vzor[$nvz++] = ['ž', '-[íy]ň', '0ně', '0ni', '0ň', '0ni', '0ni', '0ní', '-0ně', '0ní', '0ním', '0ně', '0ně', '0ních', '0němi'];
        $this->vzor[$nvz++] = ['ž', '-[íyý]ňe', '0ně', '0ni', '0ň', '0ni', '0ni', '0ní', '-0ně', '0ní', '0ním', '0ně', '0ně', '0ních', '0němi'];
        $this->vzor[$nvz++] = ['ž', '-[ťďž]', '0e', '0i', '0', '0i', '0i', '0í', '-0e', '0í', '0ím', '0e', '0e', '0ích', '0emi'];
        $this->vzor[$nvz++] = ['ž', '-toř', 'toře', 'toři', 'toř', 'toři', 'toři', 'toří', '-toře', 'toří', 'tořím', 'toře', 'toře', 'tořích', 'tořemi'];
        $this->vzor[$nvz++] = ['ž', '-ep', 'epi', 'epi', 'ep', 'epi', 'epi', 'epí', 'epi', 'epí', 'epím', 'epi', 'epi', 'epích', 'epmi'];

        // vzor kost
        $this->vzor[$nvz++] = ['ž', '-st', 'sti', 'sti', 'st', 'sti', 'sti', 'stí', '-sti', 'stí', 'stem', 'sti', 'sti', 'stech', 'stmi'];
        $this->vzor[$nvz++] = ['ž', 'ves', 'vsi', 'vsi', 'ves', 'vsi', 'vsi', 'vsí', 'vsi', 'vsí', 'vsem', 'vsi', 'vsi', 'vsech', 'vsemi'];

        // vzor Amadeus, Celsius, Kumulus, rektikulum, praktikum
        $this->vzor[$nvz++] = ['m', '-[e]us', '0a', '0u/0ovi', '0a', '0e', '0u/0ovi', '0em', '0ové', '0ů', '0ům', '0y', '0ové', '0ích', '0y'];
        $this->vzor[$nvz++] = ['m', '-[i]us', '0a', '0u/0ovi', '0a', '0e', '0u/0ovi', '0em', '0ové', '0ů', '0ům', '0usy', '0ové', '0ích', '0usy'];
        $this->vzor[$nvz++] = ['m', '-[i]s', '0se', '0su/0sovi', '0se', '0se/0si', '0su/0sovi', '0sem', '0sy/0sové', '0sů', '0sům', '0sy', '0sy/0ové', '0ech', '0sy'];
        $this->vzor[$nvz++] = ['m', 'výtrus', 'výtrusu', 'výtrusu', 'výtrus', 'výtruse', 'výtrusu', 'výtrusem', 'výtrusy', 'výtrusů', 'výtrusům', 'výtrusy', 'výtrusy', 'výtrusech', 'výtrusy'];
        $this->vzor[$nvz++] = ['m', 'trus', 'trusu', 'trusu', 'trus', 'truse', 'trusu', 'trusem', 'trusy', 'trusů', 'trusům', 'trusy', 'trusy', 'trusech', 'trusy'];
        $this->vzor[$nvz++] = ['m', '-[aeioumpts][lnmrktp]us', '10u/10a', '10u/10ovi', '10us/10a', '10e', '10u/10ovi', '10em', '10y/10ové', '10ů', '10ům', '10y', '10y/10ové', '10ech', '10y'];
        $this->vzor[$nvz++] = ['s', '-[l]um', '0a', '0u', '0um', '0um', '0u', '0em', '0a', '0', '0ům', '0a', '0a', '0ech', '0y'];
        $this->vzor[$nvz++] = ['s', '-[k]um', '0a', '0u', '0um', '0um', '0u', '0em', '0a', '0', '0ům', '0a', '0a', '0cích', '0y'];
        $this->vzor[$nvz++] = ['s', '-[i]um', '0a', '0u', '0um', '0um', '0u', '0em', '0a', '0í', '0ům', '0a', '0a', '0iích', '0y'];
        $this->vzor[$nvz++] = ['s', '-[i]um', '0a', '0u', '0um', '0um', '0u', '0em', '0a', '0ejí', '0ům', '0a', '0a', '0ejích', '0y'];
        $this->vzor[$nvz++] = ['s', '-io', '0a', '0u', '0', '0', '0u', '0em', '0a', '0í', '0ům', '0a', '0a', '0iích', '0y'];

        // vzor sedlák
        $this->vzor[$nvz++] = ['m', '-[aeiouyáéíóúý]r', '0ru/0ra', '0ru/0rovi', '0r/0ra', '0re', '0ru/0rovi', '0rem', '-0ry/-0rové', '0rů', '0rům', '0ry', '0ry/0rové', '0rech', '0ry'];
        // $this->vzor[$nvz++] = Array( 'm','-[aeiouyáéíóúý]r','0ru/0ra','0ru/0rovi','0r/0ra','0re','0ru/0rovi','0rem',     '-0ry/-0ři','0rů','0rům','0ry','0ry/0ři', '0rech','0ry' );
        $this->vzor[$nvz++] = ['m', '-r', 'ru/ra', 'ru/rovi', 'r/ra', 'ře', 'ru/rovi', 'rem', '-ry/-rové', 'rů', 'rům', 'ry', 'ry/rové', 'rech', 'ry'];
        // $this->vzor[$nvz++] = Array( 'm','-r',              'ru/ra',  'ru/rovi',  'r/ra',  'ře', 'ru/rovi',   'rem',     '-ry/-ři', 'rů','rům','ry',    'ry/ři',  'rech', 'ry' );
        $this->vzor[$nvz++] = ['m', '-[mnp]en', '0enu/0ena', '0enu/0enovi', '0en/0na', '0ene', '0enu/0enovi', '0enem', '-0eny/0enové', '0enů', '0enům', '0eny', '0eny/0enové', '0enech', '0eny'];
        $this->vzor[$nvz++] = ['m', '-[bcčdstvz]en', '0nu/0na', '0nu/0novi', '0en/0na', '0ne', '0nu/0novi', '0nem', '-0ny/0nové', '0nů', '0nům', '0ny', '0ny/0nové', '0nech', '0ny'];
        $this->vzor[$nvz++] = ['m', '-[dglmnpbtvzs]', '0u/0a', '0u/0ovi', '0/0a', '0e', '0u/0ovi', '0em', '-0y/0ové', '0ů', '0ům', '0y', '0y/0ové', '0ech', '0y'];
        $this->vzor[$nvz++] = ['m', '-[x]', '0u/0e', '0u/0ovi', '0/0e', '0i', '0u/0ovi', '0em', '-0y/0ové', '0ů', '0ům', '0y', '0y/0ové', '0ech', '0y'];
        $this->vzor[$nvz++] = ['m', 'sek', 'seku/seka', 'seku/sekovi', 'sek/seka', 'seku', 'seku/sekovi', 'sekem', 'seky/sekové', 'seků', 'sekům', 'seky', 'seky/sekové', 'secích', 'seky'];
        $this->vzor[$nvz++] = ['m', 'výsek', 'výseku/výseka', 'výseku/výsekovi', 'výsek/výseka', 'výseku', 'výseku/výsekovi', 'výsekem', 'výseky/výsekové', 'výseků', 'výsekům', 'výseky', 'výseky/výsekové', 'výsecích', 'výseky'];
        $this->vzor[$nvz++] = ['m', 'zásek', 'záseku/záseka', 'záseku/zásekovi', 'zásek/záseka', 'záseku', 'záseku/zásekovi', 'zásekem', 'záseky/zásekové', 'záseků', 'zásekům', 'záseky', 'záseky/zásekové', 'zásecích', 'záseky'];
        $this->vzor[$nvz++] = ['m', 'průsek', 'průseku/průseka', 'průseku/průsekovi', 'průsek/průseka', 'průseku', 'průseku/průsekovi', 'průsekem', 'průseky/průsekové', 'průseků', 'výsekům', 'průseky', 'průseky/průsekové', 'průsecích', 'průseky'];
        $this->vzor[$nvz++] = ['m', '-[cčšždnňmpbrstvz]ek', '0ku/0ka', '0ku/0kovi', '0ek/0ka', '0ku', '0ku/0kovi', '0kem', '-0ky/0kové', '0ků', '0kům', '0ky', '0ky/0kové', '0cích', '0ky'];
        $this->vzor[$nvz++] = ['m', '-[k]', '0u/0a', '0u/0ovi', '0/0a', '0u', '0u/0ovi', '0em', '-0y/0ové', '0ů', '0ům', '0y', '0y/0ové', 'cích', '0y'];
        $this->vzor[$nvz++] = ['m', '-ch', 'chu/cha', 'chu/chovi', 'ch/cha', 'chu/cha', 'chu/chovi', 'chem', '-chy/chové', 'chů', 'chům', 'chy', 'chy/chové', 'ších', 'chy'];
        $this->vzor[$nvz++] = ['m', '-[h]', '0u/0a', '0u/0ovi', '0/0a', '0u', '0u/0ovi', '0em', '-0y/0ové', '0ů', '0ům', '0y', '0y/0ové', 'zích', '0y'];
        $this->vzor[$nvz++] = ['m', '-e[mnz]', '0u/0a', '0u/0ovi', 'e0/e0a', '0e', '0u/0ovi', '0em', '-0y/0ové', '0ů', '0ům', '0y', '0y/0ové', '0ech', '0y'];

        // vzor muž
        $this->vzor[$nvz++] = ['m', '-ec', 'ce', 'ci/covi', 'ec/ce', 'če', 'ci/covi', 'cem', '-ce/cové', 'ců', 'cům', 'ce', 'ce/cové', 'cích', 'ci'];
        $this->vzor[$nvz++] = ['m', '-[cčďšňřťž]', '0e', '0i/0ovi', '0e', '0i', '0i/0ovi', '0em', '-0e/0ové', '0ů', '0ům', '0e', '0e/0ové', '0ích', '0i'];
        $this->vzor[$nvz++] = ['m', '-oj', 'oje', 'oji/ojovi', 'oj/oje', 'oji', 'oji/ojovi', 'ojem', '-oje/ojové', 'ojů', 'ojům', 'oje', 'oje/ojové', 'ojích', 'oji'];

        // vzory pro přetypování rodu
        $this->vzor[$nvz++] = ['m', '-[gh]a', '0y', '0ovi', '0u', '0o', '0ovi', '0ou', '0ové', '0ů', '0ům', '0y', '0ové', 'zích', '0y'];
        $this->vzor[$nvz++] = ['m', '-[k]a', '0y', '0ovi', '0u', '0o', '0ovi', '0ou', '0ové', '0ů', '0ům', '0y', '0ové', 'cích', '0y'];
        $this->vzor[$nvz++] = ['m', '-a', 'y', 'ovi', 'u', 'o', 'ovi', 'ou', 'ové', 'ů', 'ům', 'y', 'ové', 'ech', 'y'];

        $this->vzor[$nvz++] = ['ž', '-l', 'le', 'li', 'l', 'li', 'li', 'lí', 'le', 'lí', 'lím', 'le', 'le', 'lích', 'lemi'];
        $this->vzor[$nvz++] = ['ž', '-í', 'í', 'í', 'í', 'í', 'í', 'í', 'í', 'ích', 'ím', 'í', 'í', 'ích', 'ími'];
        $this->vzor[$nvz++] = ['ž', '-[jř]', '0e', '0i', '0', '0i', '0i', '0í', '0e', '0í', '0ím', '0e', '0e', '0ích', '0emi'];
        $this->vzor[$nvz++] = ['ž', '-[č]', '0i', '0i', '0', '0i', '0i', '0í', '0i', '0í', '0ím', '0i', '0i', '0ích', '0mi'];
        $this->vzor[$nvz++] = ['ž', '-[š]', '0i', '0i', '0', '0i', '0i', '0í', '0i', '0í', '0ím', '0i', '0i', '0ích', '0emi'];

        $this->vzor[$nvz++] = ['s', '-[sljřň]e', '0ete', '0eti', '0e', '0e', '0eti', '0etem', '0ata', '0at', '0atům', '0ata', '0ata', '0atech', '0aty'];
        // $this->vzor[$nvz++] = Array( 'ž','-cí',        'cí', 'cí',  'cí', 'cí', 'cí', 'cí',   'cí', 'cích', 'cím', 'cí', 'cí', 'cích', 'cími' );

        // čaj, prodej, Ondřej, žokej
        $this->vzor[$nvz++] = ['m', '-j', 'je', 'ji', 'j', 'ji', 'ji', 'jem', 'je/jové', 'jů', 'jům', 'je', 'je/jové', 'jích', 'ji'];

        // Josef, Detlef, ... ?
        $this->vzor[$nvz++] = ['m', '-f', 'fa', 'fu/fovi', 'f/fa', 'fe', 'fu/fovi', 'fem', 'fy/fové', 'fů', 'fům', 'fy', 'fy/fové', 'fech', 'fy'];

        // zbroj, výzbroj, výstroj, trofej, neteř
        // jiří, podkoní, ... ?
        $this->vzor[$nvz++] = ['m', '-í', 'ího', 'ímu', 'ího', 'í', 'ímu', 'ím', 'í', 'ích', 'ím', 'í', 'í', 'ích', 'ími'];

        // Hugo
        $this->vzor[$nvz++] = ['m', '-go', 'a', 'govi', 'ga', 'ga', 'govi', 'gem', 'gové', 'gů', 'gům', 'gy', 'gové', 'zích', 'gy'];

        // Kvido
        $this->vzor[$nvz++] = ['m', '-o', 'a', 'ovi', 'a', 'a', 'ovi', 'em', 'ové', 'ů', 'ům', 'y', 'ové', 'ech', 'y'];


        // doplňky
        // některá pomnožná jména
        $this->vzor[$nvz++] = ['?', '-[tp]y', '?', '?', '?', '?', '?', '?', '-0y', '0', '0ům', '0y', '0y', '0ech', '0ami'];
        $this->vzor[$nvz++] = ['?', '-[k]y', '?', '?', '?', '?', '?', '?', '-0y', 'e0', '0ám', '0y', '0y', '0ách', '0ami'];

        // změny rodu
        $this->vzor[$nvz++] = ['ž', '-ar', 'ary', 'aře', 'ar', 'ar', 'ar', 'ar', 'ary', 'ar', 'arám', 'ary', 'ary', 'arách', 'arami'];
        $this->vzor[$nvz++] = ['ž', '-am', 'am', 'am', 'am', 'am', 'am', 'am', 'am', 'am', 'am', 'am', 'am', 'am', 'am'];
        $this->vzor[$nvz++] = ['ž', '-er', 'er', 'er', 'er', 'er', 'er', 'er', 'ery', 'er', 'erám', 'ery', 'ery', 'erách', 'erami'];

        $this->vzor[$nvz] = ['m', '-oe', 'oema', 'oemovi', 'oema', 'oeme', 'emovi', 'emem', 'oemové', 'oemů', 'oemům', 'oemy', 'oemové', 'oemech', 'oemy'];

        $this->aCmpReg = [];
        //$nCmpReg = 0;
        $this->aCmpReg[0] = '';
        $this->aCmpReg[1] = '';
        $this->aCmpReg[2] = '';
        $this->aCmpReg[3] = '';
        $this->aCmpReg[4] = '';
        $this->aCmpReg[5] = '';
        $this->aCmpReg[6] = '';
        $this->aCmpReg[7] = '';
        $this->aCmpReg[8] = '';
        $this->aCmpReg[9] = '';

        //  Výjimky:
        //  v1 - přehlásky
        // :  důl ... dol, stůl ... stol, nůž ... nož, hůl ... hole, půl ... půle
        $nv1 = 0;
        $this->v1 = [];
        //                      1.p   náhrada   4.p.
        //
        $this->v1[$nv1++] = ['osel', 'osl', 'osla'];
        $this->v1[$nv1++] = ['karel', 'karl', 'karla'];
        $this->v1[$nv1++] = ['Karel', 'Karl', 'Karla'];
        $this->v1[$nv1++] = ['pavel', 'pavl', 'pavla'];
        $this->v1[$nv1++] = ['Pavel', 'Pavl', 'Pavla'];
        $this->v1[$nv1++] = ['Havel', 'Havl', 'Havla'];
        $this->v1[$nv1++] = ['havel', 'havl', 'havla'];
        $this->v1[$nv1++] = ['Bořek', 'Bořk', 'Bořka'];
        $this->v1[$nv1++] = ['bořek', 'bořk', 'bořka'];
        $this->v1[$nv1++] = ['Luděk', 'Luďk', 'Luďka'];
        $this->v1[$nv1++] = ['luděk', 'luďk', 'luďka'];
        $this->v1[$nv1++] = ['pes', 'ps', 'psa'];
        $this->v1[$nv1++] = ['pytel', 'pytl', 'pytel'];
        $this->v1[$nv1++] = ['ocet', 'oct', 'octa'];
        $this->v1[$nv1++] = ['chléb', 'chleb', 'chleba'];
        $this->v1[$nv1++] = ['chleba', 'chleb', 'chleba'];
        $this->v1[$nv1++] = ['pavel', 'pavl', 'pavla'];
        $this->v1[$nv1++] = ['kel', 'kl', 'kel'];
        $this->v1[$nv1++] = ['sopel', 'sopl', 'sopel'];
        $this->v1[$nv1++] = ['kotel', 'kotl', 'kotel'];
        $this->v1[$nv1++] = ['posel', 'posl', 'posla'];
        $this->v1[$nv1++] = ['důl', 'dol', 'důl'];
        $this->v1[$nv1++] = ['sůl', 'sole', 'sůl'];
        $this->v1[$nv1++] = ['vůl', 'vol', 'vola'];
        $this->v1[$nv1++] = ['půl', 'půle', 'půli'];
        $this->v1[$nv1++] = ['hůl', 'hole', 'hůl'];
        $this->v1[$nv1++] = ['stůl', 'stol', 'stůl'];
        $this->v1[$nv1++] = ['líh', 'lih', 'líh'];
        $this->v1[$nv1++] = ['sníh', 'sněh', 'sníh'];
        $this->v1[$nv1++] = ['zář', 'záře', 'zář'];
        $this->v1[$nv1++] = ['svatozář', 'svatozáře', 'svatozář'];
        $this->v1[$nv1++] = ['kůň', 'koň', 'koně'];
        $this->v1[$nv1++] = ['tůň', 'tůňe', 'tůň'];
        // --- !
        $this->v1[$nv1++] = ['prsten', 'prstýnek', 'prstýnku'];
        $this->v1[$nv1++] = ['smrt', 'smrť', 'smrt'];
        $this->v1[$nv1++] = ['vítr', 'větr', 'vítr'];
        $this->v1[$nv1++] = ['stupeň', 'stupň', 'stupeň'];
        $this->v1[$nv1++] = ['peň', 'pň', 'peň'];
        $this->v1[$nv1++] = ['cyklus', 'cykl', 'cyklus'];
        $this->v1[$nv1++] = ['dvůr', 'dvor', 'dvůr'];
        $this->v1[$nv1++] = ['zeď', 'zď', 'zeď'];
        $this->v1[$nv1++] = ['účet', 'účt', 'účet'];
        $this->v1[$nv1++] = ['mráz', 'mraz', 'mráz'];
        $this->v1[$nv1++] = ['hnůj', 'hnoj', 'hnůj'];
        $this->v1[$nv1++] = ['skrýš', 'skrýše', 'skrýš'];
        $this->v1[$nv1++] = ['nehet', 'neht', 'nehet'];
        $this->v1[$nv1++] = ['veš', 'vš', 'veš'];
        $this->v1[$nv1++] = ['déšť', 'dešť', 'déšť'];
        $this->v1[$nv1] = ['myš', 'myše', 'myš'];

        // v10 - zmena rodu na muzsky
        $this->v10 = [];
        $nv10 = 0;
        $this->v10[$nv10++] = 'sleď';
        $this->v10[$nv10++] = 'saša';
        $this->v10[$nv10++] = 'Saša';
        $this->v10[$nv10++] = 'dešť';
        $this->v10[$nv10++] = 'koň';
        $this->v10[$nv10++] = 'chlast';
        $this->v10[$nv10++] = 'plast';
        $this->v10[$nv10++] = 'termoplast';
        $this->v10[$nv10++] = 'vězeň';
        $this->v10[$nv10++] = 'sťežeň';
        $this->v10[$nv10++] = 'papež';
        $this->v10[$nv10++] = 'ďeda';
        $this->v10[$nv10++] = 'zeť';
        $this->v10[$nv10++] = 'háj';
        $this->v10[$nv10++] = 'lanýž';
        $this->v10[$nv10++] = 'sluha';
        $this->v10[$nv10++] = 'muž';
        $this->v10[$nv10++] = 'velmož';
        $this->v10[$nv10++] = 'Maťej';
        $this->v10[$nv10++] = 'maťej';
        $this->v10[$nv10++] = 'táta';
        $this->v10[$nv10++] = 'kolega';
        $this->v10[$nv10++] = 'mluvka';
        $this->v10[$nv10++] = 'strejda';
        $this->v10[$nv10++] = 'polda';
        $this->v10[$nv10++] = 'moula';
        $this->v10[$nv10++] = 'šmoula';
        $this->v10[$nv10++] = 'slouha';
        $this->v10[$nv10++] = 'drákula';
        $this->v10[$nv10++] = 'test';
        $this->v10[$nv10++] = 'rest';
        $this->v10[$nv10++] = 'trest';
        $this->v10[$nv10++] = 'arest';
        $this->v10[$nv10++] = 'azbest';
        $this->v10[$nv10++] = 'ametyst';
        $this->v10[$nv10++] = 'chřest';
        $this->v10[$nv10++] = 'protest';
        $this->v10[$nv10++] = 'kontest';
        $this->v10[$nv10++] = 'motorest';
        $this->v10[$nv10++] = 'most';
        $this->v10[$nv10++] = 'host';
        $this->v10[$nv10++] = 'kříž';
        $this->v10[$nv10++] = 'stupeň';
        $this->v10[$nv10++] = 'peň';
        $this->v10[$nv10++] = 'čaj';
        $this->v10[$nv10++] = 'prodej';
        $this->v10[$nv10++] = 'výdej';
        $this->v10[$nv10++] = 'výprodej';
        $this->v10[$nv10++] = 'ďej';
        $this->v10[$nv10++] = 'zloďej';
        $this->v10[$nv10++] = 'žokej';
        $this->v10[$nv10++] = 'hranostaj';
        $this->v10[$nv10++] = 'dobroďej';
        $this->v10[$nv10++] = 'darmoďej';
        $this->v10[$nv10++] = 'čaroďej';
        $this->v10[$nv10++] = 'koloďej';
        $this->v10[$nv10++] = 'sprej';
        $this->v10[$nv10++] = 'displej';
        $this->v10[$nv10++] = 'Aleš';
        $this->v10[$nv10++] = 'aleš';
        $this->v10[$nv10++] = 'Ambrož';
        $this->v10[$nv10++] = 'ambrož';
        $this->v10[$nv10++] = 'Tomáš';
        $this->v10[$nv10++] = 'Lukáš';
        $this->v10[$nv10++] = 'Tobiáš';
        $this->v10[$nv10++] = 'Jiří';
        $this->v10[$nv10++] = 'tomáš';
        $this->v10[$nv10++] = 'lukáš';
        $this->v10[$nv10++] = 'tobiáš';
        $this->v10[$nv10++] = 'jiří';
        $this->v10[$nv10++] = 'podkoní';
        $this->v10[$nv10++] = 'komoří';
        $this->v10[$nv10++] = 'Jirka';
        $this->v10[$nv10++] = 'jirka';
        $this->v10[$nv10++] = 'Ilja';
        $this->v10[$nv10++] = 'ilja';
        $this->v10[$nv10++] = 'Pepa';
        $this->v10[$nv10++] = 'Ondřej';
        $this->v10[$nv10++] = 'ondřej';
        $this->v10[$nv10++] = 'Andrej';
        $this->v10[$nv10++] = 'andrej';
        //  $this->v10[$nv10++] = 'josef';
        $this->v10[$nv10++] = 'mikuláš';
        $this->v10[$nv10++] = 'Mikuláš';
        $this->v10[$nv10++] = 'Mikoláš';
        $this->v10[$nv10++] = 'mikoláš';
        $this->v10[$nv10++] = 'Kvido';
        $this->v10[$nv10++] = 'kvido';
        $this->v10[$nv10++] = 'Hugo';
        $this->v10[$nv10++] = 'hugo';
        $this->v10[$nv10++] = 'Oto';
        $this->v10[$nv10++] = 'oto';
        $this->v10[$nv10++] = 'Otto';
        $this->v10[$nv10++] = 'otto';
        $this->v10[$nv10++] = 'Alexej';
        $this->v10[$nv10++] = 'alexej';
        $this->v10[$nv10++] = 'Ivo';
        $this->v10[$nv10++] = 'ivo';
        $this->v10[$nv10++] = 'Bruno';
        $this->v10[$nv10++] = 'bruno';
        $this->v10[$nv10++] = 'Alois';
        $this->v10[$nv10++] = 'alois';
        $this->v10[$nv10++] = 'bartoloměj';
        $this->v10[$nv10++] = 'Bartoloměj';
        $this->v10[$nv10++] = 'noe';
        $this->v10[$nv10] = 'Noe';

        // v11 - zmena rodu na zensky
        $this->v11 = [];
        $nv11 = 0;
        $this->v11[$nv11++] = 'vš';
        $this->v11[$nv11++] = 'dešť';
        $this->v11[$nv11++] = 'zteč';
        $this->v11[$nv11++] = 'řeč';
        $this->v11[$nv11++] = 'křeč';
        $this->v11[$nv11++] = 'kleč';
        $this->v11[$nv11++] = 'maštal';
        $this->v11[$nv11++] = 'vš';
        $this->v11[$nv11++] = 'kancelář';
        $this->v11[$nv11++] = 'závěj';
        $this->v11[$nv11++] = 'zvěř';
        $this->v11[$nv11++] = 'sbeř';
        $this->v11[$nv11++] = 'neteř';
        $this->v11[$nv11++] = 'ves';
        $this->v11[$nv11++] = 'rozkoš';
        // $this->v11[$nv11++] = 'myša';
        $this->v11[$nv11++] = 'postel';
        $this->v11[$nv11++] = 'prdel';
        $this->v11[$nv11++] = 'koudel';
        $this->v11[$nv11++] = 'koupel';
        $this->v11[$nv11++] = 'ocel';
        $this->v11[$nv11++] = 'digestoř';
        $this->v11[$nv11++] = 'konzervatoř';
        $this->v11[$nv11++] = 'oratoř';
        $this->v11[$nv11++] = 'zbroj';
        $this->v11[$nv11++] = 'výzbroj';
        $this->v11[$nv11++] = 'výstroj';
        $this->v11[$nv11++] = 'trofej';
        $this->v11[$nv11++] = 'obec';
        $this->v11[$nv11++] = 'otep';
        $this->v11[$nv11++] = 'Miriam';
        // $this->v11[$nv11++] = 'miriam';
        $this->v11[$nv11++] = 'Ester';
        $this->v11[$nv11] = 'Dagmar';
        // $this->v11[$nv11++] = 'transmise'

        // v12 - zmena rodu na stredni
        $this->v12 = [];
        $nv12 = 0;
        $this->v12[$nv12++] = 'nemluvňe';
        $this->v12[$nv12++] = 'slůně';
        $this->v12[$nv12++] = 'kůzle';
        $this->v12[$nv12++] = 'sele';
        $this->v12[$nv12++] = 'osle';
        $this->v12[$nv12++] = 'zvíře';
        $this->v12[$nv12++] = 'kuře';
        $this->v12[$nv12++] = 'tele';
        $this->v12[$nv12++] = 'prase';
        $this->v12[$nv12++] = 'house';
        $this->v12[$nv12] = 'vejce';


        // v0 - nedořešené výjimky
        $this->v0 = [];
        $nv0 = 0;
        $this->v0[$nv0] = 'sten';
        //  $this->v0[nv0++] = 'Ester'
        //  $this->v0[nv0++] = 'Dagmar'
        //  $this->v0[nv0++] = 'ovoce'
        //  $this->v0[nv0++] = 'Zeus'
        //  $this->v0[nv0++] = 'zbroj'
        //  $this->v0[nv0++] = 'výzbroj'
        //  $this->v0[nv0++] = 'výstroj'
        //  $this->v0[nv0++] = 'obec'
        //  $this->v0[nv0++] = 'konzervatoř'
        //  $this->v0[nv0++] = 'digestoř'
        //  $this->v0[nv0++] = 'humus'
        //  $this->v0[nv0++] = 'muka'
        //  $this->v0[nv0++] = 'noe'
        //  $this->v0[nv0++] = 'Noe'
        // $this->v0[nv0++] = 'Miriam'
        // $this->v0[nv0++] = 'miriam'

        // Je Nikola ženské nebo mužské jméno??? (podobně Sáva)
        // v3 - různé odchylky ve skloňování
        //    - časem by bylo vhodné opravit
        $nv3 = 0;
        $this->v3 = [];
        $this->v3[$nv3++] = 'jméno';
        $this->v3[$nv3++] = 'myš';
        $this->v3[$nv3++] = 'vězeň';
        $this->v3[$nv3++] = 'sťežeň';
        $this->v3[$nv3++] = 'oko';
        $this->v3[$nv3++] = 'sole';
        $this->v3[$nv3++] = 'šach';
        $this->v3[$nv3++] = 'veš';
        $this->v3[$nv3++] = 'myš';
        $this->v3[$nv3++] = 'klášter';
        $this->v3[$nv3++] = 'kněz';
        $this->v3[$nv3++] = 'král';
        $this->v3[$nv3++] = 'zď';
        $this->v3[$nv3++] = 'sto';
        $this->v3[$nv3++] = 'smrt';
        $this->v3[$nv3++] = 'leden';
        $this->v3[$nv3++] = 'len';
        $this->v3[$nv3++] = 'les';
        $this->v3[$nv3++] = 'únor';
        $this->v3[$nv3++] = 'březen';
        $this->v3[$nv3++] = 'duben';
        $this->v3[$nv3++] = 'květen';
        $this->v3[$nv3++] = 'červen';
        $this->v3[$nv3++] = 'srpen';
        $this->v3[$nv3++] = 'říjen';
        $this->v3[$nv3++] = 'pantofel';
        $this->v3[$nv3++] = 'žába';
        $this->v3[$nv3++] = 'zoja';
        $this->v3[$nv3++] = 'Zoja';
        $this->v3[$nv3++] = 'Zoe';
        $this->v3[$nv3] = 'zoe';

        // Ve zvl. pripadech je mozne pomoci teto promenne 'pretypovat' rod jmena
        $this->PrefRod = '0'; // smi byt '0', 'm', 'ž', 's'


        $this->astrTvar = ['', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];
    }

    //
    //  Fce isShoda vraci index pri shode koncovky
    //
    //
    //

    /**
     * Funkce vraci index pri shode koncovky (napr. isShoda('-lo','kolo'), isShoda('ko-lo','motovidlo'))
     * nebo pri rovnosti slov (napr. isShoda('molo','molo'). Jinak je navratova hodnota -1.
     *
     * @param string $vz Vzor
     * @param string $txt Text
     *
     * @return int Index shody nebo -1
     */
    private function _isShoda($vz, $txt)
    {
        $txt = mb_strtolower($txt, 'UTF-8');
        $vz = mb_strtolower($vz, 'UTF-8');
        $i = mb_strlen($vz, 'UTF-8');
        $j = mb_strlen($txt, 'UTF-8');

        if ($i === 0 || $j === 0) {
            return -1;
        }
        $i--;
        $j--;

        $nCmpReg = 0;

        while ($i >= 0 && $j >= 0) {
            if (mb_substr($vz, $i, 1, 'UTF-8') === ']') {
                $i--;
                $quit = 1;
                while ($i >= 0 && mb_substr($vz, $i, 1, 'UTF-8') !== '[') {
                    if (mb_substr($vz, $i, 1, 'UTF-8') === mb_substr($txt, $j, 1, 'UTF-8')) {
                        $quit = 0;
                        $this->aCmpReg[$nCmpReg] = mb_substr($vz, $i, 1, 'UTF-8');
                        $nCmpReg++;
                    }
                    $i--;
                }

                if ($quit === 1) {
                    return -1;
                }
            } else {
                if (mb_substr($vz, $i, 1, 'UTF-8') === '-') {
                    return $j + 1;
                }
                if (mb_substr($vz, $i, 1, 'UTF-8') !== mb_substr($txt, $j, 1, 'UTF-8')) {
                    return -1;
                }
            }
            $i--;
            $j--;
        }
        if ($i < 0 && $j < 0) {
            return 0;
        }
        if (mb_substr($vz, $i, 1, 'UTF-8') === '-') {
            return 0;
        }

        return -1;
    }

    /**
     * Transformace: ďi,ťi,ňi,ďe,ťe,ňe ... di,ti,ni,dě,tě,ně + 'ch' -> '#'
     *
     * @param string $txt2 Vstupni text
     *
     * @return string Transformovany text
     */
    private function _xDetene($txt2)
    {
        $XdeteneRV = '';
        for ($XdeteneI = 0; $XdeteneI < mb_strlen($txt2, 'UTF-8') - 1; $XdeteneI++) {
            $condition = mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') === 'e' || mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') === 'i' || mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') === 'í';
            if ($condition && mb_substr($txt2, $XdeteneI, 1, 'UTF-8') === 'ď') {
                $XdeteneRV .= 'd';
                if (mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') === 'e') {
                    $XdeteneRV .= 'ě';
                    $XdeteneI++;
                }
            } else if ($condition && mb_substr($txt2, $XdeteneI, 1, 'UTF-8') === 'ť') {
                $XdeteneRV .= 't';
                if (mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') === 'e') {
                    $XdeteneRV .= 'ě';
                    $XdeteneI++;
                }
            } else if ($condition && mb_substr($txt2, $XdeteneI, 1, 'UTF-8') === 'ň') {
                $XdeteneRV .= 'n';
                if (mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') === 'e') {
                    $XdeteneRV .= 'ě';
                    $XdeteneI++;
                }
            } else {
                $XdeteneRV .= mb_substr($txt2, $XdeteneI, 1, 'UTF-8');
            }
        }

        if ($XdeteneI === mb_strlen($txt2, 'UTF-8') - 1) {
            $XdeteneRV .= mb_substr($txt2, $XdeteneI, 1, 'UTF-8');
        }

        return $XdeteneRV;
    }

    /**
     * Transformace: di,ti,ni,dě,tě,ně ... ďi,ťi,ňi,ďe,ťe,ňe
     *
     * @param string $txt2 Vstupni text
     *
     * @return string Transformovany text
     */
    private function _xEdeten($txt2)
    {
        $XdeteneRV = '';
        for ($XdeteneI = 0; $XdeteneI < mb_strlen($txt2, 'UTF-8') - 1; $XdeteneI++) {
            $condition = mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') === 'ě' || mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') === 'i';
            if ($condition && mb_substr($txt2, $XdeteneI, 1, 'UTF-8') === 'd') {
                $XdeteneRV .= 'ď';
                if (mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') === 'ě') {
                    $XdeteneRV .= 'e';
                    $XdeteneI++;
                }
            } else if ($condition && mb_substr($txt2, $XdeteneI, 1, 'UTF-8') === 't') {
                $XdeteneRV .= 'ť';
                if (mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') === 'ě') {
                    $XdeteneRV .= 'e';
                    $XdeteneI++;
                }
            } else if ($condition && mb_substr($txt2, $XdeteneI, 1, 'UTF-8') === 'n') {
                $XdeteneRV .= 'ň';
                if (mb_substr($txt2, $XdeteneI + 1, 1, 'UTF-8') === 'ě') {
                    $XdeteneRV .= 'e';
                    $XdeteneI++;
                }
            } else {
                $XdeteneRV .= mb_substr($txt2, $XdeteneI, 1, 'UTF-8');
            }
        }

        if ($XdeteneI === mb_strlen($txt2, 'UTF-8') - 1) {
            $XdeteneRV .= mb_substr($txt2, $XdeteneI, 1, 'UTF-8');
        }

        return $XdeteneRV;
    }

    /**
     * Funkce pro sklonovani
     *
     * @param string $txt Vstupni text
     *
     * @return string Vystupni text
     */
    private function _cmpFrm($txt)
    {
        $CmpFrmRV = '';
        $length = mb_strlen($txt, 'UTF-8');
        for ($CmpFrmI = 0; $CmpFrmI < $length; $CmpFrmI++) {
            if (mb_substr($txt, $CmpFrmI, 1, 'UTF-8') === '0') {
                $CmpFrmRV .= $this->aCmpReg[0];
            } elseif (mb_substr($txt, $CmpFrmI, 1, 'UTF-8') === '1') {
                $CmpFrmRV .= $this->aCmpReg[1];
            } elseif (mb_substr($txt, $CmpFrmI, 1, 'UTF-8') === '2') {
                $CmpFrmRV .= $this->aCmpReg[2];
            } else {
                $CmpFrmRV .= mb_substr($txt, $CmpFrmI, 1, 'UTF-8');
            }
        }

        return $CmpFrmRV;
    }

    /**
     * Funkce pro sklonovani slova do daneho podle daneho vzoru
     *
     * @param int $nPad Cislo padu
     * @param int $vzndx ??
     * @param string $txt Vstupni text
     * @param bool $zivotne Zda je slovo zivotne
     *
     * @return string
     */
    private function _sklon($nPad, $vzndx, $txt, $zivotne = false)
    {

        if ($vzndx < 0 || $vzndx >= \count($this->vzor)) {
            return '???';
        }

        $txt3 = $this->_xEdeten($txt);
        $kndx = $this->_isShoda($this->vzor[$vzndx][1], $txt3);
        if ($kndx < 0 || $nPad < 1 || $nPad > 14) {
            //8-14 je pro plural
            return '???';
        }
        if ($this->vzor[$vzndx][$nPad] === '?') {
            return '?';
        }

        if (!$this->isDbgMode & $nPad === 1) {
            // 1. pad nemenime
            $rv = $this->_xDetene($txt3);
        } else {
            $rv = $this->_leftStr($kndx, $txt3) . '-' . $this->_cmpFrm($this->vzor[$vzndx][$nPad]);
        }

        if ($this->isDbgMode) {
            //preskoceni filtrovani
            return $rv;
        }
        // Formatovani zivotneho sklonovani
        // - nalezeni pomlcky
        $length = mb_strlen($rv, 'UTF-8');
        for ($nnn = 0; $nnn < $length; $nnn++) {
            if (mb_substr($rv, $nnn, 1, 'UTF-8') === '-') {
                break;
            }
        }
        $ndx1 = $nnn;

        // - nalezeni lomitka
        for ($nnn = 0; $nnn < $length; $nnn++) {
            if (mb_substr($rv, $nnn, 1, 'UTF-8') === '/') {
                break;
            }
        }
        $ndx2 = $nnn;

        if ($ndx1 !== $length && $ndx2 !== $length) {
            if ($zivotne) {
                // 'text-xxx/yyy' -> 'textyyy'
                $rv = $this->_leftStr($ndx1, $rv) . $this->_rightStr($ndx2 + 1, $rv);
            } else {
                // 'text-xxx/yyy' -> 'text-xxx'
                $rv = $this->_leftStr($ndx2, $rv);
            }
            $length = mb_strlen($rv, 'UTF-8');
        }

        // vypusteni pomocnych znaku
        $txt3 = '';
        for ($nnn = 0; $nnn < $length; $nnn++) {
            $subStr = mb_substr($rv, $nnn, 1, 'UTF-8');
            if (!($subStr === '-' || $subStr === '/')) {
                $txt3 .= mb_substr($rv, $nnn, 1, 'UTF-8');
            }
        }
        $rv = $this->_xDetene($txt3);

        return $rv;
        //  return $this->LeftStr( $kndx, $txt ) + $this->vzor[$vzndx][$nPad];
    }

    /**
     * Vrati levou cast retezce od urcite pozice (bez teto pozice)
     *
     * @param int $n Index
     * @param string $txt Vstupni text
     *
     * @return string Oriznuty retezec
     */
    private function _leftStr($n, $txt)
    {
        $rv = '';
        for ($i = 0; $i < $n && $i < mb_strlen($txt, 'UTF-8'); $i++) {
            $rv .= mb_substr($txt, $i, 1, 'UTF-8');
        }

        return $rv;
    }

    /**
     * Vrati pravou cast retezce od urcite pozice
     *
     * @param int $n Index
     * @param string $txt Vstupni text
     *
     * @return string Oriznuty retezec
     */
    private function _rightStr($n, $txt)
    {
        $rv = '';
        $length = mb_strlen($txt, 'UTF-8');
        for ($i = $n; $i < $length; $i++) {
            $rv .= mb_substr($txt, $i, 1, 'UTF-8');
        }

        return $rv;
    }

    /**
     * Rozdeli text na slova
     *
     * @param string $txt Vstupni text
     *
     * @return array Text rozdeleny na slova
     */
    private function _txtSplit($txt)
    {
        $skp = 1;
        $rv = [];

        $rvx = 0;
        $acc = '';

        $length = mb_strlen($txt, 'UTF-8');
        for ($i = 0; $i < $length; $i++) {
            if (mb_substr($txt, $i, 1, 'UTF-8') === ' ') {
                if ($skp) {
                    continue;
                }
                $skp = 1;
                $rv[$rvx++] = $acc;
                $acc = '';
                continue;
            }
            $skp = 0;
            $acc .= mb_substr($txt, $i, 1, 'UTF-8');
        }
        if (!$skp) {
            $rv[$rvx] = $acc;
        }
        return $rv;
    }

    /**
     * Sklonuje vstupni text
     *
     * @param string $text Vstupni text
     * @param bool $zivotne Sklonovat jako zivotne
     * @param string $preferovanyRod Preferovany rod nebo prazdny retezec pro autodetekci
     *
     * @return array Vysklonovany vstupni text (vsechny pady v poli)
     */
    public function inflect($text, $zivotne = false, $preferovanyRod = '')
    {
        $aTxt = $this->_txtSplit($text);

        $this->PrefRod = '0';
        $out = [];
        for ($i = \count($aTxt) - 1; $i >= 0; $i--) {
            // vysklonovani
            $this->_skl2($aTxt[$i], $preferovanyRod, $zivotne);

            // vynuceni rodu podle posledniho slova
            if ($i === \count($aTxt) - 1) {
                $this->PrefRod = $this->astrTvar[0];
            }
            // pokud nenajdeme vzor tak nesklonujeme
            if ($i < \count($aTxt) - 1 && mb_substr($this->PrefRod, 0, 1, 'UTF-8') !== '?' && mb_substr($this->astrTvar[0], 0, 1, 'UTF-8') === '?') {
                for ($j = 1; $j < 15; $j++) {
                    $this->astrTvar[$j] = $aTxt[$i];
                }
            }

            if (mb_substr($this->astrTvar[0], 0, 1, 'UTF-8') === '?') {
                $this->astrTvar[0] = '';
            }
            if ($i < \count($aTxt)) {
                for ($j = 1; $j < 15; $j++) {
                    @$out[$j] = $this->astrTvar[$j] . ' ' . @$out[$j];
                }
            } else {
                for ($j = 1; $j < 15; $j++) {
                    @$out[$j] = $this->astrTvar[$j];
                }
            }
        }

        return $out;
    }

    // Sklonovani podle standardniho seznamu pripon
    private function _sklStd($slovo, $ii, $zivotne)
    {

        if ($ii < 0 || $ii > \count($this->vzor)) {
            $this->astrTvar[0] = '!!!???';
        }
        // - seznam nedoresenych slov
        $count = \count($this->v0);
        for ($jj = 0; $jj < $count; $jj++) {
            if ($this->_isShoda($this->v0[$jj], $slovo) >= 0) {
                //str = 'Seznam výjimek [' + $jj + ']. '
                //alert(str + 'Lituji, toto $slovo zatím neumím správně vyskloňovat.');
                return null;
            }
        }
        // nastaveni rodu
        $this->astrTvar[0] = $this->vzor[$ii][0];

        // vlastni sklonovani
        for ($jj = 1; $jj < 15; $jj++) {
            $this->astrTvar[$jj] = $this->_sklon($jj, $ii, $slovo, $zivotne);
        }
        // - seznam nepresneho sklonovani
        $count = \count($this->v3);
        for ($jj = 0; $jj < $count; $jj++) {
            if ($this->_isShoda($this->v3[$jj], $slovo) >= 0) {
                //alert('Pozor, v některých pádech nemusí být skloňování tohoto slova přesné.');
                return;
            }
        }
        //  return SklFmt( $this->astrTvar );
    }

    // Pokud je index>=0, je $slovo výjimka ze seznamu '$vx'(v10,...), definovaného výše.
    private function _ndxInVx($vx, $slovo)
    {
        $count = \count($vx);
        for ($vxi = 0; $vxi < $count; $vxi++) {
            if ($slovo === $vx[$vxi]) {
                return $vxi;
            }
        }

        return -1;
    }

    // Pokud je index>=0, je $slovo výjimka ze seznamu '$vx', definovaného výše.
    private function _ndxV1($slovo)
    {
        $count = \count($this->v1);
        for ($this->_v1i = 0; $this->_v1i < $count; $this->_v1i++) {
            if ($slovo === $this->v1[$this->_v1i][0]) {
                return $this->_v1i;
            }
        }
        return -1;
    }

    private function _stdNdx($slovo)
    {
        $count = \count($this->vzor);
        for ($iii = 0; $iii < $count; $iii++) {
            // filtrace rodu
            $subStr = mb_substr($this->PrefRod, 0, 1, 'UTF-8');
            if ($subStr !== '0' && $subStr !== mb_substr($this->vzor[$iii][0], 0, 1, 'UTF-8')) {
                continue;
            }
            if ($this->_isShoda($this->vzor[$iii][1], $slovo) >= 0) {
                break;
            }
        }

        if ($iii >= \count($this->vzor)) {
            return -1;
        }

        return $iii;
    }

    // Sklonovani podle seznamu vyjimek typu v1
    //        private function _sklV1($slovo, $ii, $zivotne)
    //        {
    //            $this->_sklStd($this->v1[$ii][1], $this->_stdNdx($this->v1[$ii][1]), $zivotne);
    //            $this->astrTvar[1] = $slovo; //1.p nechame jak je
    //            $this->astrTvar[4] = $this->v1[$ii][2];
    //        }

    private function _skl2($slovo, $preferovanyRod = '', $zivotne = false)
    {
        $this->astrTvar[0] = '???';
        for ($ii = 1; $ii < 15; $ii++) {
            $this->astrTvar[$ii] = '';
        }

        $flgV1 = $this->_ndxV1($slovo);
        if ($flgV1 >= 0) {
            $slovoV1 = $slovo;
            $slovo = $this->v1[$flgV1][1];
        } else {
            $slovoV1 = '';
        }
        //  if( $ii>=0 )
        //  {
        //    $this->astrTvar[1] = 'v1: ' + $ii;
        //    $this->SklV1( $slovo, $ii );
        //    return SklFmt( $this->astrTvar );
        //    return 0;
        //  }

        $slovo = $this->_xEdeten($slovo);

        //$vNdx = 0;

        // Pretypovani rodu?
        $vs = $preferovanyRod;
        if ($vs === 'z') {
            $vs = 'ž';
        }
        if ($vs === 'm' || $vs === 'ž' || $vs === 's') {
            $this->PrefRod = $vs;
        }
        //            else
        //                $vs = '';


        if ($this->_ndxInVx($this->v10, $slovo) >= 0) {
            $this->PrefRod = 'm';
        } else if ($this->_ndxInVx($this->v11, $slovo) >= 0) {
            $this->PrefRod = 'ž';
        } else if ($this->_ndxInVx($this->v12, $slovo) >= 0) {
            $this->PrefRod = 's';
        }
        // Nalezeni $this->vzoru
        $ii = $this->_stdNdx($slovo);
        if ($ii < 0) {
            //alert('Chyba: proto toto $slovo nebyl nalezen $this->vzor.');
            return -1;  //    return '\n  Sorry, nenasel jsem $this->vzor.';
        }

        // Vlastni sklonovani
        $this->_sklStd($slovo, $ii, $zivotne);

        if ($flgV1 >= 0) {
            $this->astrTvar[1] = $slovoV1; //1.p nechame jak je
            $this->astrTvar[4] = $this->v1[$flgV1][2];
        }

        return 0; //return SklFmt( $this->astrTvar ); //  return '$this->vzor: '+$this->vzor[$ii][1];
    }

    /**
     * Inflect string
     *
     * @param string $text Input string
     * @param int $case Case 1-7
     * @param bool $plural Want plural inflection
     * @param bool $animate Is word animate
     * @param string $preferredGender Preferred gender (M, Z, S) or empty string
     * @return string Inflected string or original string on error
     */
    public static function word($text, $case, $plural, $animate = false, $preferredGender = '')
    {
        if (self::$instance === null) {
            self::$instance = new Inflection();
        }

        $result = self::$instance->inflect($text, $animate, $preferredGender);

        if (14 === \\count($result)) {
            return trim($result[$case + ((int)$plural * 7)]);
        }

        return trim($text);
    }

}