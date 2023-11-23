<?= $this->Form->create(null, [
            'id' => 'office_form',
            'name' => 'office_form',
            'url' => $url,
            'type' => 'post',
            'target' => 'office_frame'
        ])
    ?>
        <?= $this->Form->hidden('access_token', ['value' => $accessToken]) ?>
        <?= $this->Form->hidden('access_token_ttl', ['value' => $ttl]) ?>
    <?= $this->Form->end() ?>

    <span id="frameholder"></span>

    <script type="text/javascript">
        var frameholder = document.getElementById('frameholder');
        var office_frame = document.createElement('iframe');
        office_frame.name = 'office_frame';
        office_frame.id = 'office_frame';
        office_frame.title = 'Office Frame';
        office_frame.setAttribute('allowfullscreen', 'true');
        office_frame.setAttribute(
            'sandbox',
            'allow-scripts allow-same-origin allow-forms allow-popups allow-top-navigation allow-popups-to-escape-sandbox allow-downloads allow-modals'
        );
        frameholder.appendChild(office_frame);
        document.getElementById('office_form').submit();
    </script>