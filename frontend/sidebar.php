

<h2><a class="sidebar_headline" href="index.php?list=games"><?=$msg->sidebar_title_games?></a></h2>
<h2><a class="sidebar_headline" href="index.php?list=user"><?=$msg->sidebar_title_user?></a></h2>
<?
if ($conf->show_sidebar_downloads) {
    ?>
    <h2><a class="sidebar_headline" href="index.php?list=maps"><?=$msg->sidebar_title_maps?></a></h2>
    <?
}
if ($conf->show_sidebar_downloads) {
    ?>
    <h2><a class="sidebar_headline" href="index.php?list=items"><?=$msg->sidebar_title_items?></a></h2>
    <?
}
if ($conf->show_sidebar_downloads) {
    ?>
    <h2><a class="sidebar_headline" href="index.php?list=downloads"><?=$msg->sidebar_title_downloads?></a></h2>
    <?
}
if ($conf->use_login) {
    ?>
    <h3><a class="sidebar_headline" href="index.php?list=logout"><?=$msg->sidebar_title_logout?></a></h3>
    <?
}
?>
