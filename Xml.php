<?php

namespace futuretek\shared;

/**
 * Class Xml
 *
 * @package futuretek\shared
 * @author  Lukas Cerny <lukas.cerny@futuretek.cz>
 * @license Apache-2.0
 * @link    http://www.futuretek.cz
 */
class Xml
{
    /**
     * Initialize SimpleXMLElement or DOMDocument from a given XML string, file path, URL or array.
     *
     * ### Usage:
     *
     * Building XML from a string:
     *
     * `$xml = Xml::build('<example>text</example>');`
     *
     * Building XML from string (output DOMDocument):
     *
     * `$xml = Xml::build('<example>text</example>', array('return' => 'domdocument'));`
     *
     * Building XML from a file path:
     *
     * `$xml = Xml::build('/path/to/an/xml/file.xml');`
     *
     * Building from a remote URL:
     *
     * `$xml = Xml::build('http://example.com/example.xml');`
     *
     * Building from an array:
     *
     * {{{
     *    $value = array(
     *        'tags' => array(
     *            'tag' => array(
     *                array(
     *                    'id' => '1',
     *                    'name' => 'defect'
     *                ),
     *                array(
     *                    'id' => '2',
     *                    'name' => 'enhancement'
     *                )
     *            )
     *        )
     *    );
     * $xml = Xml::build($value);
     * }}}
     *
     * When building XML from an array ensure that there is only one top level element.
     *
     * ### Options
     *
     * - `return` Can be 'simplexml' to return object of SimpleXMLElement or 'domdocument' to return DOMDocument.
     * - If using array as input, you can pass `options` from Xml::fromArray.
     *
     * @param mixed $input XML string, a path to a file, an URL or an array
     * @param array $options The options to use
     * @return \SimpleXMLElement|\DOMDocument SimpleXMLElement or DOMDocument
     * @throws \RuntimeException
     */
    public static function build($input, array $options = [])
    {
        if (!\is_array($options)) {
            $options = ['return' => (string)$options];
        }
        $defaults = [
            'return' => 'simplexml',
        ];
        $options = array_merge($defaults, $options);

        if (is_array($input) || is_object($input)) {
            return self::fromArray((array)$input, $options);
        }

        if (strpos($input, '<') !== false) {
            if ($options['return'] === 'simplexml' || $options['return'] === 'simplexmlelement') {
                return new \SimpleXMLElement($input, LIBXML_NOCDATA);
            }
            $dom = new \DOMDocument();
            $dom->loadXML($input);

            return $dom;
        }

        if (file_exists($input) || strpos($input, 'http://') === 0 || strpos($input, 'https://') === 0) {
            if ($options['return'] === 'simplexml' || $options['return'] === 'simplexmlelement') {
                return new \SimpleXMLElement($input, LIBXML_NOCDATA, true);
            }
            $dom = new \DOMDocument();
            $dom->load($input);

            return $dom;
        }

        if (!\is_string($input)) {
            throw new \RuntimeException(Tools::poorManTranslate('fts-shared', 'Invalid input.'));
        }
        throw new \RuntimeException(Tools::poorManTranslate('fts-shared', 'XML cannot be read.'));
    }

    /**
     * Transform an array into a SimpleXMLElement
     *
     * ### Options
     *
     * - `format` If create childs ('tags') or attributes ('attribute').
     * - `version` Version of XML document. Default is 1.0.
     * - `encoding` Encoding of XML document. If null remove from XML header. Default is the some of application.
     * - `return` If return object of SimpleXMLElement ('simplexml') or DOMDocument ('domdocument'). Default is SimpleXMLElement.
     *
     * Using the following data:
     *
     * {{{
     * $value = array(
     *    'root' => array(
     *        'tag' => array(
     *            'id' => 1,
     *            'value' => 'defect',
     *            '@' => 'description'
     *         )
     *     )
     * );
     * }}}
     *
     * Calling `Xml::fromArray($value, 'tags');`  Will generate:
     *
     * `<root><tag><id>1</id><value>defect</value>description</tag></root>`
     *
     * And calling `Xml::fromArray($value, 'attribute');` Will generate:
     *
     * `<root><tag id="1" value="defect">description</tag></root>`
     *
     * @param array $input Array with data
     * @param array $options The options to use
     * @return \SimpleXMLElement|\DOMDocument SimpleXMLElement or DOMDocument
     * @throws \RuntimeException
     */
    public static function fromArray($input, array $options = [])
    {
        if (!\is_array($input) || \count($input) !== 1) {
            throw new \RuntimeException(Tools::poorManTranslate('fts-shared', 'Invalid input.'));
        }
        $key = key($input);
        if (\is_int($key)) {
            throw new \RuntimeException(Tools::poorManTranslate('fts-shared', 'The key of input must be alphanumeric.'));
        }

        if (!\is_array($options)) {
            $options = ['format' => (string)$options];
        }
        $defaults = [
            'format' => 'tags',
            'version' => '1.0',
            'encoding' => 'utf-8',
            'return' => 'simplexml',
        ];
        $options = array_merge($defaults, $options);

        $dom = new \DOMDocument($options['version'], $options['encoding']);
        self::_fromArray($dom, $dom, $input, $options['format']);

        $options['return'] = strtolower($options['return']);
        if ($options['return'] === 'simplexml' || $options['return'] === 'simplexmlelement') {
            return new \SimpleXMLElement($dom->saveXML());
        }

        return $dom;
    }

    /**
     * Recursive method to create child from array
     *
     * @param \DOMDocument $dom Handler to DOMDocument
     * @param \DOMElement|\DOMDocument $node Handler to DOMElement (child)
     * @param array $data Array of data to append to the $node.
     * @param string $format Either 'attribute' or 'tags'.  This determines where nested keys go.
     * @return void
     * @throws \RuntimeException
     */
    protected static function _fromArray($dom, $node, &$data, $format)
    {
        if ($data === null || $data === '' || !\is_array($data)) {
            return;
        }
        foreach ($data as $key => $value) {
            if (\is_string($key)) {
                if (!\is_array($value)) {
                    if (\is_bool($value)) {
                        $value = (int)$value;
                    } elseif ($value === null) {
                        $value = '';
                    }
                    $isNamespace = strpos($key, 'xmlns:');
                    if ($isNamespace !== false) {
                        $node->setAttributeNS('http://www.w3.org/2000/xmlns/', $key, $value);
                        continue;
                    }
                    if ($key[0] !== '@' && $format === 'tags') {
                        $child = null;
                        if (!is_numeric($value)) {
                            // Escape special characters
                            // http://www.w3.org/TR/REC-xml/#syntax
                            // https://bugs.php.net/bug.php?id=36795
                            $child = $dom->createElement($key, '');
                            $child->appendChild(new \DOMText($value));
                        } else {
                            $child = $dom->createElement($key, $value);
                        }
                        $node->appendChild($child);
                    } else {
                        if ($key[0] === '@') {
                            $key = substr($key, 1);
                        }
                        $attribute = $dom->createAttribute($key);
                        $attribute->appendChild($dom->createTextNode($value));
                        $node->appendChild($attribute);
                    }
                } else {
                    if ($key[0] === '@') {
                        throw new \RuntimeException(Tools::poorManTranslate('fts-shared', 'Invalid array'));
                    }
                    if (array_keys($value) === range(0, \count($value) - 1)) { // List
                        foreach ($value as $item) {
                            $data = compact('dom', 'node', 'key', 'format');
                            $data['value'] = $item;
                            self::_createChild($data);
                        }
                    } else { // Struct
                        self::_createChild(compact('dom', 'node', 'key', 'value', 'format'));
                    }
                }
            } else {
                throw new \RuntimeException(Tools::poorManTranslate('fts-shared', 'Invalid array'));
            }
        }
    }

    /**
     * Helper to _fromArray(). It will create childs of arrays
     *
     * @param array $data Array with informations to create childs
     * @return void
     */
    protected static function _createChild($data): void
    {
        $dom = $node = $key = $value = $format = null;

        extract($data, EXTR_OVERWRITE);
        $childNS = $childValue = null;
        if (\is_array($value)) {
            if (array_key_exists('@', $value)) {
                $childValue = (string)$value['@'];
                unset($value['@']);
            }
            if (array_key_exists('xmlns:', $value)) {
                $childNS = $value['xmlns:'];
                unset($value['xmlns:']);
            }
        } elseif (!empty($value) || $value === 0) {
            $childValue = (string)$value;
        }

        if ($childValue) {
            $child = $dom->createElement($key, $childValue);
        } else {
            $child = $dom->createElement($key);
        }
        if ($childNS) {
            $child->setAttribute('xmlns', $childNS);
        }

        self::_fromArray($dom, $child, $value, $format);
        $node->appendChild($child);
    }

    /**
     * Returns this XML structure as a array.
     *
     * @param \SimpleXMLElement|\DOMDocument|\DOMNode $obj SimpleXMLElement, DOMDocument or DOMNode instance
     * @return array Array representation of the XML structure.
     * @throws \RuntimeException
     */
    public static function toArray($obj)
    {
        if ($obj instanceof \DOMNode) {
            $obj = simplexml_import_dom($obj);
        }
        if (!($obj instanceof \SimpleXMLElement)) {
            throw new \RuntimeException(Tools::poorManTranslate('fts-shared', 'The input is not instance of SimpleXMLElement, DOMDocument or DOMNode.'));
        }
        $result = [];
        $namespaces = array_merge(['' => ''], $obj->getNamespaces(true));
        self::_toArray($obj, $result, '', array_keys($namespaces));

        return $result;
    }

    /**
     * Recursive method to toArray
     *
     * @param \SimpleXMLElement $xml SimpleXMLElement object
     * @param array $parentData Parent array with data
     * @param string $ns Namespace of current child
     * @param array $namespaces List of namespaces in XML
     * @return void
     */
    protected static function _toArray($xml, &$parentData, $ns, $namespaces)
    {
        $data = [];

        foreach ($namespaces as $namespace) {
            foreach ($xml->attributes($namespace, true) as $key => $value) {
                if (!empty($namespace)) {
                    $key = $namespace . ':' . $key;
                }
                $data['@' . $key] = (string)$value;
            }

            foreach ($xml->children($namespace, true) as $child) {
                self::_toArray($child, $data, $namespace, $namespaces);
            }
        }

        $asString = trim((string)$xml);
        if (empty($data)) {
            $data = $asString;
        } elseif (!empty($asString)) {
            $data['@'] = $asString;
        }

        if (!empty($ns)) {
            $ns .= ':';
        }
        $name = $ns . $xml->getName();
        if (array_key_exists($name, $parentData)) {
            if (!\is_array($parentData[$name]) || !array_key_exists(0, $parentData[$name][0])) {
                $parentData[$name] = [$parentData[$name]];
            }
            $parentData[$name][] = $data;
        } else {
            $parentData[$name] = $data;
        }
    }

}
