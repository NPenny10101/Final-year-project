<?php

// k-means++ algorithm for clustering
function initializeCentroids(array $dataset, $k) {
$centroids = array();
$firstKey = array_rand($dataset);
$centroids[] = $dataset[$firstKey];

for ($i = 1; $i < $k; $i++) {
    $distances = array();
    foreach ($dataset as $dataPoint) {
        $minDist = INF;
        foreach ($centroids as $centroid) {
            $dist = calculateDistance($dataPoint, $centroid);
            $minDist = min($minDist, $dist);
        }
        $distances[] = $minDist;
    }
    $total = array_sum($distances);
    $probabilities = array_map(function($dist) use ($total) { return $dist / $total; }, $distances);
    $cumulativeProbabilities = array();
    $cumSum = 0;
    foreach ($probabilities as $p) {
        $cumSum += $p;
        $cumulativeProbabilities[] = $cumSum;
    }
    $random = mt_rand() / mt_getrandmax();
    foreach ($cumulativeProbabilities as $index => $cumProb) {
        if ($random <= $cumProb) {
            $centroids[] = $dataset[$index];
            break;
        }
    }
}
return $centroids;
}

function assignPointsToCentroids(array $dataset, array $centroids) {
$clusters = array();
foreach ($dataset as $point) {
    $minDist = INF;
    $cluster = 0;
    foreach ($centroids as $key => $centroid) {
        $dist = calculateDistance($point, $centroid);
        if ($dist < $minDist) {
            $minDist = $dist;
            $cluster = $key;
        }
    }
    $clusters[$cluster][] = $point; 
}
return $clusters;
}

function updateCentroids(array $clusters) {
$newCentroids = array();
foreach ($clusters as $cluster) {
    $newCentroids[] = array_map(function($dim) {
        return array_sum($dim) / count($dim);
    }, arrayTranspose($cluster));
}
return $newCentroids;
}

function arrayTranspose(array $array) {
$result = array();
foreach ($array as $sub) {
    foreach ($sub as $k => $v) {
        $result[$k][] = $v;
    }
}
return $result;
}

function calculateDistance(array $point1, array $point2) {
$metrics = ['visits', 'uniqueVisitors'];  // Only use these metrics for clustering
$sum = 0;
foreach ($metrics as $metric) {
    $sum += pow(($point1[$metric] - $point2[$metric]), 2);
}
return sqrt($sum);
}

function kMeans(array $dataset, $k, $maxIterations = 100) {
$centroids = initializeCentroids($dataset, $k);
$iterations = 0;
$clusters = array();

while ($iterations++ < $maxIterations) {
    $clusters = assignPointsToCentroids($dataset, $centroids);
    $newCentroids = updateCentroids($clusters);
    if ($newCentroids === $centroids) {
        break; // Centroids didn't change
    }
    $centroids = $newCentroids;
}

return $clusters;
}



// Find the cluster for the known web_Id
function findDataPointAndCluster(array $clusters, $knownWebId) {
$result = array();
foreach ($clusters as $clusterId => $cluster) {
    foreach ($cluster as $dataPoint) {
        if ($dataPoint['web_Id'] == $knownWebId) {
            $result['dataPoint'] = $dataPoint;
            $result['clusterId'] = $clusterId;
            return $result; // Return as soon as the desired data point is found
        }
    }
}
return $result; // Return an empty array if no matching web_Id is found
}