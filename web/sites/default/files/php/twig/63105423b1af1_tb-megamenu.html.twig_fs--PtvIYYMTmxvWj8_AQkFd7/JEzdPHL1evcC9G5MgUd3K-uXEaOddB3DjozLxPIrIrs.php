<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* modules/contrib/tb_megamenu/templates/tb-megamenu.html.twig */
class __TwigTemplate_70bd08a8fea56cd6db149bc1526eb274923d81eb9aaefd918383fb140890579b extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        if (array_key_exists("css_style", $context)) {
            // line 2
            echo "<style type=\"text/css\">
  .tb-megamenu.animate .mega > .mega-dropdown-menu, .tb-megamenu.animate.slide .mega > .mega-dropdown-menu > div {
  ";
            // line 4
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["css_style"] ?? null), 4, $this->source), "html", null, true);
            echo "
  }
</style>
";
        }
        // line 8
        echo "<div ";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["attributes"] ?? null), 8, $this->source), "html", null, true);
        echo ">
  ";
        // line 9
        if ((($context["section"] ?? null) == "frontend")) {
            // line 10
            echo "    <button data-target=\".nav-collapse\" data-toggle=\"collapse\" class=\"btn btn-navbar tb-megamenu-button\" type=\"button\">
      <i class=\"fa fa-reorder\"></i>
    </button>
    <div class=\"nav-collapse ";
            // line 13
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar((((($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 = ($context["block_config"] ?? null)) && is_array($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4) || $__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4 instanceof ArrayAccess ? ($__internal_f607aeef2c31a95a7bf963452dff024ffaeb6aafbe4603f9ca3bec57be8633f4["always-show-submenu"] ?? null) : null)) ? (" always-show") : ("")));
            echo "\">
  ";
        }
        // line 15
        echo "  ";
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["content"] ?? null), 15, $this->source), "html", null, true);
        echo "
  ";
        // line 16
        if ((($context["section"] ?? null) == "frontend")) {
            // line 17
            echo "    </div>
  ";
        }
        // line 19
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "modules/contrib/tb_megamenu/templates/tb-megamenu.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  80 => 19,  76 => 17,  74 => 16,  69 => 15,  64 => 13,  59 => 10,  57 => 9,  52 => 8,  45 => 4,  41 => 2,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "modules/contrib/tb_megamenu/templates/tb-megamenu.html.twig", "C:\\xampp1\\htdocs\\drupal_8_project\\web\\modules\\contrib\\tb_megamenu\\templates\\tb-megamenu.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 1);
        static $filters = array("escape" => 4);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['escape'],
                []
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
