set terminal png

set xdata time
set timefmt "%Y-%m-%d"
set format x "%b. %Y"
 
unset xlabel
set xtics out nomirror rotate 1 autofreq scale 1,0.5
set ylabel 'Books'
set ytics out nomirror scale 0.5
set grid ytics
 
set title 'uBook Statistics'
set output 'stat0_total.png'
plot 'statistics.log' using 1:4 with line title 'new books'

set output 'stat1_active.png'
plot 'statistics.log' using 1:3 with line title 'offers',\
 'statistics.log' using 1:5 with line title 'offerors',\
 'statistics.log' using 1:6 with line title 'images'

set output 'stat2_offers_per_person.png'
plot 'statistics.log' using 1:($3/$5) with line title 'offers per person'

set output 'stat3_fraction_of_images.png'
plot 'statistics.log' using 1:($6/$3) with line title 'fraction of images'

