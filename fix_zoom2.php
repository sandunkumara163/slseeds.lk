<?php
$file = 'resources/views/products/show.blade.php';
$content = file_get_contents($file);

$search = '/@mousemove="zoomData\.show = true; zoomData\.x = .*?"/';

$replace = '@mousemove="zoomData.show = true; let rect = $el.getBoundingClientRect(); zoomData.x = ($event.clientX - rect.left) / rect.width * 100; zoomData.y = ($event.clientY - rect.top) / rect.height * 100;"';

$content = preg_replace($search, $replace, $content, 1);
file_put_contents($file, $content);
echo "Done\n";
?>
