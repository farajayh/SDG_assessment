<?php

  //input data
  $input_data = array (
    'region' => array(
          'name' => "Africa",
          'avgAge' => 19.7,
          'avgDailyIncomeInUSD' => 5,
          'avgDailyIncomePopulation' => 0.71
        ),
    'periodType' => "days",
    'timeToElapse' =>  58,
    'reportedCases' => 674,
    'population' => 66622705,
    'totalHospitalBeds' => 1380614,
    'impact' => array(),
    'severeImpact' => array()
    );
  
  
  //estimator function
  function covid19ImpactEstimator($data)
  {
    //normalise time elapsed to days
    switch ($data['periodType']){
      case 'weeks':
        $timeToElapse = $data['timeToElapse']*7;
        break;
      case 'months':
        $timeToElapse = $data['timeToElapse']*30;
        break;
      default:
        $timeToElapse = $data['timeToElapse'];
      
    }

    //currently infected
    $impactCurrentlyInfected = $data['reportedCases']*10;
    $severeImpactCurrentlyInfected = $data['reportedCases']*50;

    //infections by requested time 
    $impactInfectionsByRequestedTime = $impactCurrentlyInfected * (2**intval($timeToElapse/3));
    $severeImpactInfectionsByRequestedTime = $severeImpactCurrentlyInfected * (2**intval($timeToElapse/3));

    //severe cases by requested time
    $impactSevereCasesByRequestedTime = intval((15/100)*$impactInfectionsByRequestedTime);
    $severeImpactSevereCasesByRequestedTime = intval((15/100)*$severeImpactInfectionsByRequestedTime);

    //hospital bed by requested time
    $impactHospitalBedsByRequestedTime = intval(((35/100)*$data['totalHospitalBeds']) - $impactSevereCasesByRequestedTime);
    $severeImpactHospitalBedsByRequestedTime = intval(((35/100)*$data['totalHospitalBeds']) - $severeImpactSevereCasesByRequestedTime);

    //cases for ICU by requested time
    $impactCasesForICUByRequestedTime = intval((5/100)*$impactInfectionsByRequestedTime);
    $severeImpactCasesForICUByRequestedTime = intval((5/100)*$severeImpactInfectionsByRequestedTime);

    //cases for ventilators by requested time
    $impactCasesForVentilatorsByRequestedTime = intval((2/100)*$impactInfectionsByRequestedTime);
    $severeImpactCasesForVentilatorsByRequestedTime = intval((2/100)*$severeImpactInfectionsByRequestedTime);

    //dollars in flight
    $impactDollarsInFlight = intval(($impactInfectionsByRequestedTime * $data['region']['avgDailyIncomePopulation'] * $data['region']['avgDailyIncomeInUSD'])/$timeToElapse);
    $severeImpactDollarsInFlight = intval(($severeImpactInfectionsByRequestedTime * $data['region']['avgDailyIncomePopulation'] * $data['region']['avgDailyIncomeInUSD'])/$timeToElapse);

    //output
    $data = array(
                        'data' => $data,
                        'impact' => array(
                                          'currentlyInfected' => $impactCurrentlyInfected,
                                          'infectionsByRequestedTime' =>  $impactInfectionsByRequestedTime,
                                          'severeCasesByRequestedTime' => $impactSevereCasesByRequestedTime,
                                          'hospitalBedsByRequestedTime' => $impactHospitalBedsByRequestedTime,
                                          'casesForICUByRequestedTime' => $impactCasesForICUByRequestedTime,
                                          'casesForVentilatorsByRequestedTime' => $impactCasesForVentilatorsByRequestedTime,
                                          'dollarsInFlight' => $impactDollarsInFlight
                        ),
                        'severeImpact' => array(
                                          'currentlyInfected' => $severeImpactCurrentlyInfected,
                                          'infectionsByRequestedTime' =>  $severeImpactInfectionsByRequestedTime,
                                          'severeCasesByRequestedTime' => $severeImpactSevereCasesByRequestedTime,
                                          'hospitalBedsByRequestedTime' => $severeImpactHospitalBedsByRequestedTime,
                                          'casesForICUByRequestedTime' => $severeImpactCasesForICUByRequestedTime,
                                          'casesForVentilatorsByRequestedTime' => $severeImpactCasesForVentilatorsByRequestedTime,
                                          'dollarsInFlight' => $severeImpactDollarsInFlight
                        ),
                  );
    return $data;
  }

print_r(covid19ImpactEstimator($input_data));

?>