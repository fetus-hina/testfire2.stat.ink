{{strip}}
  {{set layout="main.tpl"}}
  {{set title="{{$app->name}} | {{'Color-Blind Support'|translate:'app'}}"}}
  <div class="container">
    <h1>
      {{'Color-Blind Support'|translate:'app'|escape}}
    </h1>

    {{AdWidget}}
    {{SnsWidget}}

    <p>
      {{$app->name|escape}}では、色覚サポートを実装しています。
    </p>
    <p>
      色覚サポート設定をオンにすると、一部の配色が変更になります。
    </p>
    <p>
      色覚サポートをオンにするには、次の手順で操作してください。
    </p>
    <ol>
      <li>
        ページ上部のナビゲーションバーの「ゲスト」または「ユーザ名」が表示されている箇所をクリック/タップします。
        （スマホ等、画面幅が狭い場合は右上のメニューボタンをまず押してください）
      </li>
      <li>
        「{{'Color-Blind Support'|translate:'app'|escape}}」をクリック/タップします。
      </li>
      <li>
        画面が再読み込みされ、色覚サポートモードで動作します。
      </li>
    </ol>
    <p>
      設定はブラウザごとに保存されます。
      環境ごとに設定頂く必要はありますが、一度設定すると以後の設定は必要ありません。
      また、設定内容はブラウザから外には送信されません。
    </p>
    <p>
      ※現在、一部のページでまだ色覚サポート設定への対応が行えていません。
      （バトル詳細内のチームカラー、グラフ等）
    </p>

    <hr>

    <p>
      配色は<a href="http://jfly.iam.u-tokyo.ac.jp/colorset/">カラーユニバーサルデザイン奨配色セット</a>を参考にしています。
    </p>
    <p>
      色弱者にどのように見えるかの確認にはAndroidの<a href="https://play.google.com/store/apps/details?id=asada0.android.cvsimulator">色のシミュレータ</a>を利用しています。
    </p>
    <p>
      実際には個人差もあるでしょうし、何より実際に直接自分の目で確認することができないため見やすくなっているかどうか確証は持てていません。
      もしこの組み合わせは見づらいというのが発生している場合はご連絡頂けると助かります。
    </p>
  </div>
{{/strip}}
