<div class="container-fluid listViewPageDiv detailViewContainer" id="calendar-horizontal-settings">
    <div class=" vt-default-callout vt-info-callout">
        <h4 class="vt-callout-header"><span class="fa fa-info-circle"></span>Info</h4>
        <p>{vtranslate('LBL_SETTINGS_INFO', $QUALIFIED_MODULE)}</p>
    </div>

    <div class="full-width">
        <div class="col-sm-12 col-xs-12 ">
    <form class="form-inline" id="EditView" name="EditView" method="post" action="index.php">
        <input type=hidden name="entities" id="entities" value="{$COUNT_ENTITY}" />
        <input type="hidden" name="module" value="{$MODULE_NAME}" />
        <input type="hidden" value="Settings" name="parent" />
        <input type="hidden" name="action" value="SaveSettings" />


        <div class="listViewContentDiv row" id="listViewContents">
            <div class="row marginBottom10px">
                <div class="row">
                    <div class="row marginBottom10px">
                        <div class="col-sm-4 textAlignRight">{vtranslate('LBL_API_KEY',$QUALIFIED_MODULE)}</div>
                        <div class="fieldValue col-sm-6">
                            <input name="api" id="api" type="text" value="{$ENTITY.api}" />
                        </div>
                    </div>
                    <div class="row marginBottom10px">
                        <div class="col-sm-4 textAlignRight">{vtranslate('LBL_DELIVERY_FIELD',$QUALIFIED_MODULE)}</div>
                        <div class="fieldValue col-sm-6">
                            <select name="del_field" id="del_field" class="chzn-select select2" style="width: 150px">
                                {foreach item=VFIELD from=$VFIELDS}
                                    <option value="{$VFIELD->getName()}" {if $VFIELD->getName() eq $ENTITY.del_field}selected{/if} >{$VFIELD->get('label')}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="row marginBottom10px">
                        <div class="col-sm-4 textAlignRight">{vtranslate('LBL_ORG_FIELD',$QUALIFIED_MODULE)}</div>
                        <div class="fieldValue col-sm-6">
                            <select name="org_field" id="org_field" class="chzn-select select2" style="width: 150px">
                                {foreach item=FIELD key=NAME from=$FIELDS}
                                    <option value="{$NAME}" {if $NAME eq $ENTITY.org_field}selected{/if} >{$FIELD->get('label')}</option>
                                {/foreach}
                            </select>
                        </div>

                    </div>
                </div>


            </div>
        </div>

        <div class="filterActions row" style="padding: 10px 0;">
            <button type="submit" class="btn btn-success pull-right saveButton" id="save-condition-color" type="button"><strong>{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</strong></button>
        </div>
    </form>
        </div>
    </div>
</div>