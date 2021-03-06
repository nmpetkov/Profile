{adminheader}
<div class="z-admin-content-pagetitle">
    {icon type="help" size="small"}
    <h3>{gt text='About Profile'}</h3>
</div>

<p>
    {gt text="There are some details that you may know about Profile and how it let you translate the Account Properties labels and values of your site."}
    {gt text="First of all, it's recommended to move the translation files of Profile to the /config directory to custom them there."}
    {gt text="Copy all the files of the modules/zikula/profile-module/Resources/locale directory to /config/locale. There you will customize the catalog module_zikulaprofilemodule.pot and the translations afterwards."}
</p>

<h3>{gt text='About the property labels'}</h3>
<p>
    {gt text="It's recommended to use unique words for the labels, for that reason we use the format: _DUDLABEL and translate them for each available language in the site to a readable text."}
    {gt text="After you create a new property label (such as _MYDUDLABEL), you must add the msgid to your catalog (POT file). You will need two lines per label: <strong>msgid '_MYDUDLABEL'</strong> then one line after the <strong>msgstr ''</strong> (study the default catalog content)."}
    {gt text="With the updated POT, you can syncronize the translations (.PO files) for each available language, or even create a new .PO from the catalog with a tool like poEdit."}
    {gt text="In the .PO file you will be able to translate _MYDUDLABEL to the corresponding text for each specific language, to provide a legible display string like 'My DUD Label'."}
</p>

<h3>{gt text='About the property values'}</h3>
<p>
    {gt text="Some display types like checkboxes, radio buttons, dropdown lists and multicheckboxes, allows to define a specific set of options to show."}
    {gt text="Each option can be added in the catalog to be translated. Also, if you use a custom date format that needs translation, it can be added to the catalog too.<br />Enjoy!"}
</p>
{adminfooter}
