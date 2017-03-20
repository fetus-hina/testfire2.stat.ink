{{strip}}
  {{use class="rmrevin\yii\fontawesome\FontAwesome" as="FA"}}
  {{\rmrevin\yii\fontawesome\AssetBundle::register($this)|@void}}
  {{\app\assets\BattleInputAsset::register($this)|@void}}

  {{$_prefix = 'input-modal-internal'|sha1|substr:0:8}}

  {{$_agentName = $app->name|cat:' web client'}}
  {{$_agentVersion = 'v'|cat:$app->version}}
  {{$_agentRevision = \app\components\Version::getShortRevision()}}

  <div class="modal fade" id="inputModal" tabindex="-1" role="dialog" aria-labelledby="inputModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="{{'Close'|translate:'app'|escape}}">
            <span aria-hidden="true">{{FA::icon('times')->tag('span')}}</span>
          </button>
          <h4 class="modal-title" id="inputModalLabel">
            {{'Input new battle results'|translate:'app'|escape}}&#32;
            (β)
          </h4>
        </div>
        <div class="modal-body">
          <ul class="nav nav-tabs" role="tablist" style="margin-bottom:15px">
            <li role="presentation" class="active">
              <a href="#_{{$_prefix|escape}}_regular" data-toggle="tab">
                <span class="hidden-xs">{{'Global Testfire'|translate:'app-rule'|escape}}</span>
                <span class="visible-xs-inline">{{'Testfire'|translate:'app-rule'|escape}}</span>
              </a>
            </li>
          </ul>
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="_{{$_prefix|escape}}_regular">
              <form class="battle-input-form" id="battle-input-form--regular" action="#" onsubmit="return !1">
                <input type="hidden" name="apikey" value="{{$app->user->identity->api_key|escape}}">
                <input type="hidden" name="agent" value="{{$_agentName|escape}}">
                <input type="hidden" name="agent_version" value="{{$_agentVersion|escape}}" data-version="{{$_agentVersion|escape}}" data-revision="{{$_agentRevision|escape}}">

                <div class="row">
                  <div class="col-xs-6">
                    <div class="form-group">
                      <input type="hidden" id="battle-input-form--regular--rule" name="rule" value="">
                      <input type="text" id="battle-input-form--regular--rule--label" value="" class="form-control" readonly>
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div class="form-group">
                      <select id="battle-input-form--regular--lobby" name="lobby" class="form-control" readonly>
                        <option value="standard">
                          {{use class="app\models\Lobby"}}
                          {{$_lobby = Lobby::findOne(['key' => 'standard'])}}
                          {{$_lobby->name|translate:'app-rule'|escape}}
                        </option>
                      </select>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-xs-6">
                    <div class="form-group">
                      <select id="battle-input-form--regular--display" name="display" class="form-control">
                      </select>
                    </div>
                  </div>
                  <div class="col-xs-6">
                    <div class="form-group">
                      <select id="battle-input-form--regular--controller" name="controller" class="form-control">
                      </select>
                    </div>
                  </div>
                </div>

                <!--h5>{{'Weapon'|translate:'app'|escape}}</h5-->
                <div class="form-group">
                  <select class="form-control battle-input-form--weapons" id="battle-input-form--regular--weapon" name="weapon">
                  </select>
                </div>

                <!--h5>{{'Stages'|translate:'app'|escape}}</h5-->
                <div class="form-group">
                  <input type="hidden" id="battle-input-form--regular--stage" name="map" value="">
                  <div class="row">
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input-form--stages" data-game-mode="regular" data-target="battle-input-form--regular--stage">
                      </button>
                    </div>
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input-form--stages" data-game-mode="regular" data-target="battle-input-form--regular--stage">
                      </button>
                    </div>
                  </div>
                </div>

                <!--h5>{{'Result'|translate:'app'|escape}}</h5-->
                <div class="form-group">
                  <input type="hidden" id="battle-input-form--regular--result" name="result" value="">
                  <div class="row">
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input-form--result" data-target="battle-input-form--regular--result" data-value="win">
                        {{'Win'|translate:'app'|escape}}
                      </button>
                    </div>
                    <div class="col-xs-6">
                      <button type="button" class="btn btn-default btn-block battle-input-form--result" data-target="battle-input-form--regular--result" data-value="lose">
                        {{'Lose'|translate:'app'|escape}}
                      </button>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-xs-12 col-sm-6">
                    <div class="form-group">
                      <label for="battle-input-form--regular--point">
                        {{'Turf inked (including bonus)'|translate:'app'|escape}}
                      </label>
                      <input type="number" id="battle-input-form--regular--point" name="my_point" min="0" class="form-control" pattern="\d+" inputmode="numeric">
                    </div>
                  </div>
                  <div class="col-xs-6 col-sm-3">
                    <div class="form-group">
                      <label for="battle-input-form--regular--kill">
                        {{'Kills'|translate:'app'|escape}}
                      </label>
                      <input type="number" id="battle-input-form--regular--kill" name="kill" min="0" max="99" class="form-control" pattern="\d+" inputmode="numeric">
                    </div>
                  </div>
                  <div class="col-xs-6 col-sm-3">
                    <div class="form-group">
                      <label for="battle-input-form--regular--death">
                        {{'Deaths'|translate:'app'|escape}}
                      </label>
                      <input type="number" id="battle-input-form--regular--death" name="death" min="0" max="99" class="form-control" pattern="\d+" inputmode="numeric">
                    </div>
                  </div>
                </div>

                <div class="form-group text-right">
                  <input type="hidden" id="battle-input-form--regular--uuid" name="uuid" value="">
                  <button type="button" class="btn btn-primary" id="battle-input-form--regular--submit" data-form="_{{$_prefix|escape}}_regular" disabled>
                    {{FA::icon('floppy-o')->tag('span')}} {{'Save!'|translate:'app'|escape}}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
{{/strip}}
