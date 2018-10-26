# Wheel Form plugin for Craft CMS 3.x

Free Form Builder with Database Integration, successor of Free Contact Form Plugin featured on [Straight Up Craft](https://straightupcraft.com/)

![Screenshot](resources/img/form-entries.jpg)

## Requirements

This plugin requires Craft CMS 3.0.0 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require xpertbot/craft-wheelform

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Wheel Form.

## Features
- reCaptcha Validation
- Export CSV File
- Custom Email HTML Template
- Template variables for easy development
- Email Validation based on field type selected
- Required Fields
- Checkbox options
- Honeypot Field
- Ajax and Redirect friendly
- Send Form submissions to multiple emails
- Reordering of fields
- Save Uploaded files to Asset Manager
- Multiple Translations
- Export / Import Fields between different websites

## Usage
After successful installation go to Plugin Settings and add the email you would like the forms to send `FROM`. As well as set other useful settings.

Forms are administered at the forms panel main settings. Set where this form should be submitting `TO` as well as name of the form.

Field Settings can be set as Required or not, for validation purposes.

Current Field types supported are:
* Text
* Textarea
* Email
* Number
* Checkboxes
* Radio
* Select
* Hidden
* File

## Template Variables
- errors (Array of errors based on field name, form, recaptcha, honeypot).

- values (Array of User submitted values based on field name).

- wheelform
    - settings
    - form
        - recaptcha
        - open()
        - close()
        - fields
            - type
            - name
            - label
            - items
            - fieldClass
            - containerClass
            - required
            - order
            - value
        - entries
            - id
            - fields
                - name
                - label
                - value
                - type
            - date

## Template Structure

Your form template can look something like this:

```twig
{% macro errorList(errors) %}
    {% if errors %}
        <ul class="errors">
            {% for error in errors %}
                <li>{{ error }}</li>
            {% endfor %}
        </ul>
    {% endif %}
{% endmacro %}
{% from _self import errorList %}

{% set form = wheelform.form({
    id: 1,
    redirect: 'contact/thanks',
    attributes: [
        'novalidate="novalidate"'
    ],
    buttonLabel: "Submit",
}) %}

{{ form.open() }}
    {{ errors['form'] is defined ? errorList(errors['form']) }}
    {{ errors['recaptcha'] is defined ? errorList(errors['recaptcha']) }}
    {{ errors['honeypot'] is defined ? errorList(errors['honeypot']) }}

    {% for field in form.fields %}
        {{ field.render() }}
        {{ errors[field.name] is defined ? errorList(errors[field.name]) }}
    {% endfor %}
{{ form.close() }}
```
Advanced templating:

```twig
{% macro errorList(errors) %}
    {% if errors %}
        <ul class="errors">
            {% for error in errors %}
                <li>{{ error }}</li>
            {% endfor %}
        </ul>
    {% endif %}
{% endmacro %}
{% from _self import errorList %}

{% set form = wheelform.form({
    id: 1,
    redirect: 'contact/thanks',
    styleClass: "form",
    attributes: [
        'novalidate="novalidate"'
    ],
}) %}

{{ form.open() }}
    {{ errors['form'] is defined ? errorList(errors['form']) }}
    {{ errors['recaptcha'] is defined ? errorList(errors['recaptcha']) }}
    {{ errors['honeypot'] is defined ? errorList(errors['honeypot']) }}

    {% for field in form.fields %}
        {% switch field.type %}
            {% case "checkbox" %}
                <div class="form-checkbox">
                {% for item in field.items %}
                </label><input class="checkbox" type="checkbox" value="{{ item }}" {{values[field.name] is defined and item in values[field.name] ? ' checked="checked"' : '' }} name="{{field.name}}[]" id=""/>{{item}}</label>
                {% endfor %}
                </div>
            {% case "radio" %}
                <div class="form-radio">
                {% for item in field.items %}
                <input class="radio" type="radio" value="{{ item }}" {{values[field.name] is defined and item == values[field.name] ? ' checked="checked"' : '' }} name="{{field.name}}" id=""/>
                <label>{{item}}</label>
                {% endfor %}
                </div>
            {% case "select" %}
                <div class="form-select">
                <select id="wf-select" name="{{field.name}}" class="wf-field {{field.fieldClass}}">
                {% for item in field.items %}
                    <option value="{{ field.item}}" {{values[field.name] is defined and item == values[field.name] ? 'selected="selected"' : '' }}>{{item}}</option>
                {% endfor %}
                </select>
                </div>
            {% case "file" %}
                <div class="form-group">
                <label>{{field.label}}</label>
                <input type="file" name="{{field.name}}" id=""/>

                </div>
            {% case "textarea" %}
                <div class="form-group">
                    <label>{{field.label}}</label>
                    <textarea class="form-control" name="{{field.name}}" id="">{{ values[field.name] ?? '' }}</textarea>
                </div>
            {% default %}
                <div class="form-group">
                <label>{{field.label}}</label>
                <input class="form-control" type="{{field.type}}" value="{{ values[field.name] ?? '' }}" name="{{field.name}}" id=""/>
                </div>
        {% endswitch %}
        {{ errors[field.name] is defined ? errorList(errors[field.name]) }}
    {% endfor %}
    {% if form.recaptcha %}
        <div>
            <script src="https://www.google.com/recaptcha/api.js"></script>
            <!-- Production captcha -->
            <div class="g-recaptcha" data-sitekey="{{wheelform.getSettings('recaptcha_public')}}"></div>
        </div>
    {% endif %}

    <button class="btn btn-success" id="submit">Send</button>

</form>
```

If you want to stick to HTML and not use the variables:
```twig
{% macro errorList(errors) %}
    {% if errors %}
        <ul class="errors">
            {% for error in errors %}
                <li>{{ error }}</li>
            {% endfor %}
        </ul>
    {% endif %}
{% endmacro %}

{% from _self import errorList %}

    {{ errors['form'] is defined ? errorList(errors['form']) }}

    <form method="post" action="" accept-charset="UTF-8" novalidate="" enctype="multipart/form-data">
    {{ csrfInput() }}
    <input type="hidden" name="action" value="wheelform/message/send">
    <input type="hidden" name="form_id" value="1">
    <input type="hidden" name="redirect" value="{{ 'contact/thanks'|hash }}">

    <h3><label for="from-name">Your Name</label></h3>
    <input id="from-name" type="text" name="name" value="{{ values['name'] ?? '' }}">
    {{ errors['name'] is defined ? errorList(errors['name']) }}

    <h3><label for="from-email">Your Email</label></h3>
    <input id="from-email" type="email" name="email" value="{{ values['email'] ?? '' }}">
    {{ errors['email'] is defined ? errorList(errors['email']) }}

    <h3><label for="phone">Phone</label></h3>
    <input id="phone" type="text" name="phone" value="{{ values['phone'] ?? '' }}">
    {{ errors['phone'] is defined ? errorList(errors['phone']) }}

    <label><input type="checkbox" name="favorite_topping[]" value="Chocolate">Chocolate</label>
    <label><input type="checkbox" name="favorite_topping[]" value="Vanilla">Vanilla</label>
    <label><input type="checkbox" name="favorite_topping[]" value="Strawberry">Strawberry</label>
    {{ errors['favorite_topping'] is defined ? errorList(errors['favorite_topping']) }}

    <h3><label for="message">Message</label></h3>
    <textarea rows="10" cols="40" id="message" name="message">{{ values['message'] ?? '' }}</textarea>
    {{ errors['message'] is defined ? errorList(errors['message']) }}

    <input type="file" name="user_file" id="user_filer">

    {# if using recaptcha settings #}
    <div class="recaptcha-container">
        <script src="https://www.google.com/recaptcha/api.js"></script>
        <div class="g-recaptcha" data-sitekey="{{ site_key }}">
        </div>
    </div>

    <input type="submit" value="Send">
</form>
```

### Displaying existing form submissions
You can access existing submitted form entries on a form through the `form.entries` property:

```twig
{% set form = wheelform.form({ id: 1 }) %}
{% set entries = form.entries %}

<table>
    <thead>
        {% for key, fields in entries|first if key == 'fields'  %}
            <tr>
                {% for field in fields  %}
                    <th>{{ field.label }}</th>
                {% endfor %}
                <th>Date</th>
            </tr>
        {% endfor %}
    </thead>
    <tbody>
        {% for entry in entries %}
            <tr data-id="{{ entry.id }}">
                {% for field in entry.fields %}
                    <td data-id="{{ field.name }}">{{ field.value }}</td>
                {% endfor %}
                    <td data-id="date">{{ entry.date|date("m/d/Y") }}</td>
            </tr>
        {% endfor %}
    </tbody>
</table>
```


### Redirecting after submit

If you have a ‘redirect’ hidden input, the user will get redirected to it upon successfully sending the email.

Note that if you don’t include a `redirect` input, the current page will get reloaded.

### Displaying flash messages

When a contact form is submitted, the plugin will set a `notice` or `success` flash message on the user session. You can display it in your template like this:

```twig
{% if craft.app.session.hasFlash('notice') %}
    <p class="message notice">{{ craft.app.session.getFlash('notice') }}</p>
{% elseif craft.app.session.hasFlash('error') %}
    <p class="message error">{{ craft.app.session.getFlash('error') }}</p>
{% endif %}
```

### File attachments

If you would like your form to accept file attachments, follow these steps:

1. Make sure your opening HTML `<form>` tag contains `enctype="multipart/form-data"`.
2. Add a `<input type="file" name="{field_name}">` to your form.

### File saving to asset Folder
On Plugin Settings select the Folder you would like to save files. Make sure `Allow public URLs` option is turned on.


### Ajax form submissions

You can optionally post contact form submissions over Ajax if you’d like. Just send a POST request to your site with all of the same data that would normally be sent:

```js
$('#my-form').submit(function(ev) {
    // Prevent the form from actually submitting
    ev.preventDefault();

    var data = $(this).serialize();

    // Send it to the server
    $.post('/wheelform/message/send',
        data,
        function(response) {
            if (response.success) {
                //reponse.message is the message saved in the Form Settings
                $('#thanks').fadeIn();
            } else {
                // response.values will contain user submitted values
                // response.errors will be an array containing any validation errors that occurred, indexed by field name
                // e.g. response.error['email'] => ['Email is required', 'Email is invalid']
                alert('An error occurred. Please try again.');
            }
        }
    );
});
```

If using getCrsfInput() make sure you are submitting it with the rest of your form.

### Form Tools
- CSV Exporter can be based on entry date, under Admin > Utilities > Form Tools.
- Form Fields can be exported as a JSON file.
- Form Fields can be imported from a valid JSON file.

### Custom Email Template
Custom Twig templates can be used using these steps:

1. Create `wheelform.php` file inside Craft's config folder.
2. `wheelform.php` expends an array of configuration settings to be returned. Only `template` variable is required, this is a path to the custom TWIG Template. Example:

```php
return [
    'template' => '_emails/custom';
];
```
3. Inside `custom.html` (or the name you chose for your file on above config) you will have access to a `fields` array. Example

```html
<html>
<body>
    <h1>Custom Template</h1>

    <ul>
    {% for field in fields %}
        <li>
        <strong>{{ field.label }}:</strong>
        {% switch field.type %}

        {% case "file" %}
            {# This is an object with file attributes #}
            {{ field.value.name }}

            {% case "checkbox" %}
                {# Array of all choices selected #}
                {{ field.value | join(',')}}

            {% default %}

                {# Text based items #}
                {{ field. value }}

        {% endswitch %}
        </li>
    {% endfor %}
    </ul>
</body>
</html>
```

### Honeypot Field
Honeypot field is a field that is meant to be left blank by humans. Usually hidden by CSS. [More information](https://stackoverflow.com/questions/36227376/better-honeypot-implementation-form-anti-spam) about Honeypot fields.

### Events
(Note: this is mostly for developers that know basic PHP and Composer Packages)

`beforeSend` Event, this allows developers to modify the fields being sent in an email, this event does not modify the values entered in the database. Only the fields being sent to the client.

You can also trigger other custom functionality such as gathering custom field values to add to a Third party service such as a Mailing list.

`Event` class properties:
* `form_id` - Current ID of form being submitted, This allows developers some way to check what fields are being sent.
* `subject` - Subject of the currnet form. This can be modified to make it customizable.
* `message` - Associative Array of different fields with the values submitted.

Example Plugin to handle these events. [wheelformhelper](https://github.com/xpertbot/wheelformhelper)

### Translations
New translations can be submitted using the format inside the translations folder.
