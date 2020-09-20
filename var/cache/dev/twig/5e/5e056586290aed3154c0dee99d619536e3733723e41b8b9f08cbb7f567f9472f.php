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

/* security/login.html.twig */
class __TwigTemplate_54bc5dbc691b690ae6f6c90acc01bf31a3e7f81273f4883fbc42d01050470af1 extends Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->blocks = [
            'title' => [$this, 'block_title'],
            'body' => [$this, 'block_body'],
        ];
    }

    protected function doGetParent(array $context)
    {
        // line 2
        return "base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "security/login.html.twig"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "template", "security/login.html.twig"));

        $this->parent = $this->loadTemplate("base.html.twig", "security/login.html.twig", 2);
        $this->parent->display($context, array_merge($this->blocks, $blocks));
        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

    }

    // line 3
    public function block_title($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "title"));

        // line 4
        echo "    Anmelden | webWMS | Das webbasierte Lagerverwaltungssystem
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    // line 7
    public function block_body($context, array $blocks = [])
    {
        $macros = $this->macros;
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e = $this->extensions["Symfony\\Bundle\\WebProfilerBundle\\Twig\\WebProfilerExtension"];
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->enter($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02 = $this->extensions["Symfony\\Bridge\\Twig\\Extension\\ProfilerExtension"];
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->enter($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof = new \Twig\Profiler\Profile($this->getTemplateName(), "block", "body"));

        // line 8
        echo "<body>
<!-- Beginn Login-Formular -->
<div class=\"content-webLVS\">
    <div class=\"signup-form\">
        <form class=\"form-horizontal\" role=\"form\" method=\"post\">
        ";
        // line 13
        if ((isset($context["error"]) || array_key_exists("error", $context) ? $context["error"] : (function () { throw new RuntimeError('Variable "error" does not exist.', 13, $this->source); })())) {
            // line 14
            echo "            <div class=\"alert alert-danger\">
                ";
            // line 15
            echo twig_escape_filter($this->env, $this->extensions['Symfony\Bridge\Twig\Extension\TranslationExtension']->trans(twig_get_attribute($this->env, $this->source, (isset($context["error"]) || array_key_exists("error", $context) ? $context["error"] : (function () { throw new RuntimeError('Variable "error" does not exist.', 15, $this->source); })()), "messageKey", [], "any", false, false, false, 15), twig_get_attribute($this->env, $this->source, (isset($context["error"]) || array_key_exists("error", $context) ? $context["error"] : (function () { throw new RuntimeError('Variable "error" does not exist.', 15, $this->source); })()), "messageData", [], "any", false, false, false, 15), "security"), "html", null, true);
            echo "
            </div>
        ";
        }
        // line 17
        echo " ";
        if (twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 17, $this->source); })()), "user", [], "any", false, false, false, 17)) {
            // line 18
            echo "            <div class=\"mb-3\">
                You are logged in as ";
            // line 19
            echo twig_escape_filter($this->env, twig_get_attribute($this->env, $this->source, twig_get_attribute($this->env, $this->source, (isset($context["app"]) || array_key_exists("app", $context) ? $context["app"] : (function () { throw new RuntimeError('Variable "app" does not exist.', 19, $this->source); })()), "user", [], "any", false, false, false, 19), "username", [], "any", false, false, false, 19), "html", null, true);
            echo ",
                <a href=\"";
            // line 20
            echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("app_logout");
            echo "\">Logout</a>
            </div>
        ";
        }
        // line 23
        echo "        <p><img src=\"assets/images/webWMS_logo.png\" class=\"logo-webLVS\" alt=\"\" ></p>
        <h4>Das webbasierte Lagerverwaltungssystem</h4><br>
        <div class=\"form-group\">
            <input
                type=\"text\"
                value=\"";
        // line 28
        echo twig_escape_filter($this->env, (isset($context["last_username"]) || array_key_exists("last_username", $context) ? $context["last_username"] : (function () { throw new RuntimeError('Variable "last_username" does not exist.', 28, $this->source); })()), "html", null, true);
        echo "\"
                name=\"username\"
                id=\"inputUsername\"
                class=\"form-control\"
                placeholder=\"Benutzername\"
                required
                autofocus
            />
        </div>
        <div class=\"form-group\">
            <input
                type=\"password\"
                name=\"password\"
                id=\"inputPassword\"
                class=\"form-control\"
                placeholder=\"Passwort\"
                required
            />
        </div>
        <input
                type=\"hidden\"
                name=\"_csrf_token\"
                value=\"";
        // line 50
        echo twig_escape_filter($this->env, $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderCsrfToken("authenticate"), "html", null, true);
        echo "\"
        />
        <input type=\"hidden\" name=\"_target_path\" value=\"";
        // line 52
        echo $this->extensions['Symfony\Bridge\Twig\Extension\RoutingExtension']->getPath("homepage");
        echo "\" />
        <div class=\"form-group\">
            <button type=\"submit\" name=\"anmelden\" class=\"btn btn-secondary btn-lg\">Anmelden</button>
        </div>
        <div class=\"signup-form-text-info\">
            &copy; 2019-";
        // line 57
        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, "now", "Y"), "html", null, true);
        echo " <a href=\"https://softdev-nord.de\"> Softdev Nord Rene Irrgang </a><br>
            webLVS &reg; ";
        // line 58
        echo twig_escape_filter($this->env, (isset($context["appVersionNumber"]) || array_key_exists("appVersionNumber", $context) ? $context["appVersionNumber"] : (function () { throw new RuntimeError('Variable "appVersionNumber" does not exist.', 58, $this->source); })()), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, (isset($context["appVersion"]) || array_key_exists("appVersion", $context) ? $context["appVersion"] : (function () { throw new RuntimeError('Variable "appVersion" does not exist.', 58, $this->source); })()), "html", null, true);
        echo "
        </div>
        <div>
            <i class=\"brands-webLVS mdi mdi-language-php\"></i>
            <i class=\"brands-webLVS mdi mdi-language-html5\"></i>
            <i class=\"brands-webLVS mdi mdi-language-css3\"></i>
            <i class=\"brands-webLVS mdi mdi-language-javascript\"></i>
        </div>
        <div class=\"signup-form-text-software\">
            Webserver:   ";
        // line 67
        echo twig_escape_filter($this->env, (isset($context["webServer"]) || array_key_exists("webServer", $context) ? $context["webServer"] : (function () { throw new RuntimeError('Variable "webServer" does not exist.', 67, $this->source); })()), "html", null, true);
        echo " <br>
            Server IP-Adresse: ";
        // line 68
        echo twig_escape_filter($this->env, (isset($context["serverIp"]) || array_key_exists("serverIp", $context) ? $context["serverIp"] : (function () { throw new RuntimeError('Variable "serverIp" does not exist.', 68, $this->source); })()), "html", null, true);
        echo " <br>
            Server Name: ";
        // line 69
        echo twig_escape_filter($this->env, (isset($context["serverName"]) || array_key_exists("serverName", $context) ? $context["serverName"] : (function () { throw new RuntimeError('Variable "serverName" does not exist.', 69, $this->source); })()), "html", null, true);
        echo " <br>
            Freier Speicherplatz: ";
        // line 70
        echo twig_escape_filter($this->env, (isset($context["freeDiskSpace"]) || array_key_exists("freeDiskSpace", $context) ? $context["freeDiskSpace"] : (function () { throw new RuntimeError('Variable "freeDiskSpace" does not exist.', 70, $this->source); })()), "html", null, true);
        echo " <br>
            MySQL Version: ";
        // line 71
        echo twig_escape_filter($this->env, (isset($context["mySqlVersion"]) || array_key_exists("mySqlVersion", $context) ? $context["mySqlVersion"] : (function () { throw new RuntimeError('Variable "mySqlVersion" does not exist.', 71, $this->source); })()), "html", null, true);
        echo "
        </div>
        </form>
    </div>
</div>
<!-- Ende Login-Formular -->
";
        
        $__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02->leave($__internal_319393461309892924ff6e74d6d6e64287df64b63545b994e100d4ab223aed02_prof);

        
        $__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e->leave($__internal_085b0142806202599c7fe3b329164a92397d8978207a37e79d70b8c52599e33e_prof);

    }

    public function getTemplateName()
    {
        return "security/login.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  203 => 71,  199 => 70,  195 => 69,  191 => 68,  187 => 67,  173 => 58,  169 => 57,  161 => 52,  156 => 50,  131 => 28,  124 => 23,  118 => 20,  114 => 19,  111 => 18,  108 => 17,  102 => 15,  99 => 14,  97 => 13,  90 => 8,  80 => 7,  69 => 4,  59 => 3,  36 => 2,);
    }

    public function getSourceContext()
    {
        return new Source("{# templates/security/login.html.twig #}
{% extends 'base.html.twig' %}
{% block title %}
    Anmelden | webWMS | Das webbasierte Lagerverwaltungssystem
{% endblock %}

{% block body %}
<body>
<!-- Beginn Login-Formular -->
<div class=\"content-webLVS\">
    <div class=\"signup-form\">
        <form class=\"form-horizontal\" role=\"form\" method=\"post\">
        {% if error %}
            <div class=\"alert alert-danger\">
                {{ error.messageKey|trans(error.messageData, 'security') }}
            </div>
        {% endif %} {% if app.user %}
            <div class=\"mb-3\">
                You are logged in as {{ app.user.username }},
                <a href=\"{{ path('app_logout') }}\">Logout</a>
            </div>
        {% endif %}
        <p><img src=\"assets/images/webWMS_logo.png\" class=\"logo-webLVS\" alt=\"\" ></p>
        <h4>Das webbasierte Lagerverwaltungssystem</h4><br>
        <div class=\"form-group\">
            <input
                type=\"text\"
                value=\"{{ last_username }}\"
                name=\"username\"
                id=\"inputUsername\"
                class=\"form-control\"
                placeholder=\"Benutzername\"
                required
                autofocus
            />
        </div>
        <div class=\"form-group\">
            <input
                type=\"password\"
                name=\"password\"
                id=\"inputPassword\"
                class=\"form-control\"
                placeholder=\"Passwort\"
                required
            />
        </div>
        <input
                type=\"hidden\"
                name=\"_csrf_token\"
                value=\"{{ csrf_token('authenticate') }}\"
        />
        <input type=\"hidden\" name=\"_target_path\" value=\"{{ path('homepage') }}\" />
        <div class=\"form-group\">
            <button type=\"submit\" name=\"anmelden\" class=\"btn btn-secondary btn-lg\">Anmelden</button>
        </div>
        <div class=\"signup-form-text-info\">
            &copy; 2019-{{ \"now\"|date(\"Y\") }} <a href=\"https://softdev-nord.de\"> Softdev Nord Rene Irrgang </a><br>
            webLVS &reg; {{ appVersionNumber }} {{ appVersion }}
        </div>
        <div>
            <i class=\"brands-webLVS mdi mdi-language-php\"></i>
            <i class=\"brands-webLVS mdi mdi-language-html5\"></i>
            <i class=\"brands-webLVS mdi mdi-language-css3\"></i>
            <i class=\"brands-webLVS mdi mdi-language-javascript\"></i>
        </div>
        <div class=\"signup-form-text-software\">
            Webserver:   {{ webServer }} <br>
            Server IP-Adresse: {{ serverIp }} <br>
            Server Name: {{ serverName }} <br>
            Freier Speicherplatz: {{ freeDiskSpace }} <br>
            MySQL Version: {{ mySqlVersion }}
        </div>
        </form>
    </div>
</div>
<!-- Ende Login-Formular -->
{% endblock %}
", "security/login.html.twig", "/var/www/html/webWMS/templates/security/login.html.twig");
    }
}
