<?php

declare(strict_types=1);

namespace WebWMS\Helper\Development\RectorCustomRule;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\AttributeGroup;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\Class_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\Exception\PoorDocumentationException;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ConvertPhpDocToClassAttributeRector extends AbstractRector
{
    private const ROOT_NAMESPACE = '\WebWMS\Helper\Attribute';

    /**
     * @throws PoorDocumentationException
     */
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert PHPDoc block with @package, @author, @copyright, and Class to #[ClassInformation] attribute',
            []
        );
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Class_) {
            return null;
        }

        if ($this->hasClassInformationAttribute($node)) {
            return null;
        }

        $docComment = $node->getDocComment();
        if (!$docComment instanceof Doc) {
            return null;
        }

        $docText = trim($docComment->getText());
        if ($docText === '' || !str_starts_with($docText, '/**')) {
            return null;
        }

        $docText = $docComment->getText();

        $package = $this->extractPhpDocTagValue($docText, '@package');
        $author = $this->extractPhpDocTagValue($docText, '@author');
        $copyright = $this->extractPhpDocTagValue($docText, '@copyright');
        $class = $node->name?->toString();
        $covers = $this->extractPhpDocTagValue($docText, '@covers');

        if ($package === null && $author === null && $copyright === null) {
            return null;
        }

        $attributeArguments = array_values(array_filter([
            $this->createAttributeArg('package', $package),
            $this->createAttributeArg('author', $author),
            $this->createAttributeArg('copyright', $copyright),
            $this->createAttributeArg('class', $class),
            $this->createAttributeArg('covers', $covers),
        ]));

        $attribute = new Attribute(new Name(self::ROOT_NAMESPACE . '\ClassInformation'), $attributeArguments);
        $attributeGroup = new AttributeGroup([$attribute]);

        array_unshift($node->attrGroups, $attributeGroup);

        $node->setDocComment(new Doc(''));

        // Return the formatted node
        return $node;
    }

    private function createAttributeArg(string $name, ?string $value): ?Arg
    {
        if ($value === null) {
            return null;
        }

        return new Arg(new String_($value), false, false, [], new Identifier($name));
    }

    private function extractPhpDocTagValue(string $docComment, string $tag): ?string
    {
        if (preg_match('/' . preg_quote($tag, '/') . '\s*:\s*(.+)/', $docComment, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    /**
     * Überprüft, ob die Klasse bereits das ClassInformation-Attribut hat.
     */
    private function hasClassInformationAttribute(Class_ $class): bool
    {
        // Sicherstellen, dass $attrGroups ein Array ist
        $attrGroups = $class->getAttribute('attrGroups', []);

        // Wenn $attrGroups kein Array ist, zurückgeben, dass das Attribut nicht vorhanden ist
        if (!is_array($attrGroups)) {
            return false;
        }

        // Überprüfen, ob das ClassInformation-Attribut existiert
        foreach ($attrGroups as $attrGroup) {
            // Sicherstellen, dass $attributeGroup ein Objekt ist, das die Eigenschaft 'attrs' besitzt
            if (is_object($attrGroup) && property_exists($attrGroup, 'attrs')) {
                foreach ($attrGroup->attrs as $attribute) {
                    if ($attribute->name->toString() === 'ClassInformation') {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
