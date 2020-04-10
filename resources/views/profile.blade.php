@extends(app('oxygen.layout'))

@section('content')

<?php

    use Oxygen\Core\Html\Form\Label;
    use Oxygen\Core\Html\Form\Row;
    use Oxygen\Core\Html\Header\Header;
    use Oxygen\Core\Html\Form\StaticField;

    $header = Header::fromBlueprint(
        $blueprint,
        __('oxygen/mod-auth::ui.profile.title')
    );

?>

<!-- =====================
            HEADER
     ===================== -->

<div class="Block">
    {!! $header->render() !!}

    <?php
        foreach($fields->getFields() as $field) {
            $field = StaticField::fromEntity($field, $user, true);
            $row = new Row([new Label($field->getMeta()), $field]);
            echo $row->render();
        }
    ?>

    <!-- =====================
            CHANGE PASSWORD
         ===================== -->

    <?php
        $toolbarItem = $blueprint->getToolbarItem('getChangePassword');
        $toolbarItem->label = 'Change your password now';
        $toolbarItem->color = 'blue';
    ?>

    @if($toolbarItem->shouldRender())

        <div class="Row">
            <h2 class="heading-gamma">Change Password</h2>
        </div>
        <div class="Row">
            <div class="Form-content">
                <p>
                    Choosing a strong password will help keep your account safe.<br>
                    Try to use as many different characters, numbers and symbols as you possibly can, and make sure you don't use the password anywhere else.
                </p>
                <br>
                <?php
                    echo $toolbarItem->render();
                ?>
            </div>
        </div>


    @endif

    <!-- =====================
           TERMINATE ACCOUNT
         ===================== -->

    <?php
        $toolbarItem = $blueprint->getToolbarItem('deleteForce');
    ?>

    @if($toolbarItem->shouldRender())

        <div class="Row">
            <h2 class="heading-gamma">Terminate Account</h2>
        </div>
        <div class="Row">
            <div class="Form-content">
                <p>If you are sure you delete <strong>your entire account and everything associated with it</strong>, then click the button below.</p>
                <br>
                <?php
                    echo $toolbarItem->render();
                ?>
            </div>
        </div>


    @endif

</div>

@stop
