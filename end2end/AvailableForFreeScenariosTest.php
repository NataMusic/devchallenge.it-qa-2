<?php

/*
 * This file is part of the OpenWeatherMap project.
 */

namespace end2end;

include('APITestCase.php');

/**
 * Class AvailableForFreeScenariosTest.
 */
class AvailableForFreeScenariosTest extends APITestCase
{
    /**
     * Flow:
     * - Get weather by city name
     * - Check response
     * - Get forecast by city name
     */
    public function testGetWeatherAndForecastByCityName()
    {
        $weatherResponse = $this->request('GET', 'weather?q=Kiev,ua');
        $this->assertWeatherDataStructure($weatherResponse, 'Kiev');

        $forecastResponse = $this->request('GET', 'forecast?q=Kiev,ua');
        $this->assertForecastDataStructure($forecastResponse);
    }

    /**
     * Flow:
     * - Get weather by city name
     * - Get weather for nearest 5 cities
     * - Get forecast for 4th city from the list
     */
    public function testGetWeatherForCities()
    {
        $weatherResponse = $this->request('GET', 'weather?q=Kiev');
        $weather = $this->assertWeatherDataStructure($weatherResponse, 'Kiev');
        $lon = $weather['coord']['lon'];
        $lat = $weather['coord']['lat'];

        $citiesInCycleResponse = $this->request('GET', sprintf('find?lat=%s&lon=%s&cnt=5', $lat, $lon));
        $citiesInCycle = $this->getContent($citiesInCycleResponse);
        $this->assertStatusCode($citiesInCycleResponse, 200);
        $this->assertArrayHasKey('list', $citiesInCycle);
        $this->assertCount(5, $citiesInCycle['list']);
        $this->assertSame('Podol', $citiesInCycle['list'][3]['name']);
        $podolId = $citiesInCycle['list'][3]['id'];

        $forecastResponse = $this->request('GET', sprintf('forecast?id=%s', $podolId));
        $this->assertForecastDataStructure($forecastResponse);
    }

    /**
     * Flow:
     * - Get weather by zip code
     * - Get forecast by zip code
     */
    public function testGetWeatherAndForecastByZip()
    {
        $weatherResponse = $this->request('GET', 'weather?zip=10024,us');
        $this->assertWeatherDataStructure($weatherResponse, 'New York');

        $forecastResponse = $this->request('GET', 'forecast?zip=10024,us');
        $this->assertForecastDataStructure($forecastResponse);
    }

    /**
     * Flow:
     * - Get weather by city id
     * - Check description language
     * - Get weather by city id with lang = 'ru'
     * - Check description language
     */
    public function testGetWeatherWithTranslation()
    {
        $weatherResponse = $this->request('GET', 'weather?id=2172797');
        $weather = $this->assertWeatherDataStructure($weatherResponse, 'Cairns');
        $descriptionEnglish = $this->getWeatherDescription($weather);
        $this->assertLessThanOrEqual(127, ord($descriptionEnglish));

        $weatherRuResponse = $this->request('GET', 'weather?id=2172797&lang=ru');
        $weatherRu = $this->assertWeatherDataStructure($weatherRuResponse, 'Cairns');
        $descriptionRussian = $this->getWeatherDescription($weatherRu);
        $this->assertGreaterThanOrEqual(128, ord($descriptionRussian));
    }

    /**
     * Check that content contains keys and they are not empty.
     *
     * @param array $content
     * @param array $keys
     */
    private function assertContentHasNotEmptyKeys($content, $keys)
    {
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $content);
            $this->assertNotEmpty($content[$key]);
        }
    }

    /**
     * Basic check for forecast response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    private function assertForecastDataStructure($response)
    {
        $forecast = $this->getContent($response);
        $this->assertStatusCode($response, 200);
        $this->assertArrayHasKey('list', $forecast);
        $this->assertContentHasNotEmptyKeys(
            $forecast['list'][0],
            ['weather', 'main', 'wind', 'clouds', 'dt', 'sys', 'dt_txt']
        );
    }

    /**
     * Basic check for current weather response.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param string                              $cityName
     *
     * @return mixed
     */
    private function assertWeatherDataStructure($response, $cityName)
    {
        $this->assertStatusCode($response, 200);
        $weather = $this->getContent($response);
        $this->assertArrayHasKey('coord', $weather);
        $this->assertContentHasNotEmptyKeys(
            $weather,
            ['coord', 'weather', 'base', 'main', 'visibility', 'wind', 'clouds', 'dt', 'sys', 'id', 'name', 'cod']
        );
        $this->assertContentHasNotEmptyKeys($weather['coord'], ['lon', 'lat']);

        $this->assertSame($cityName, $weather['name']);

        return $weather;
    }

    /**
     * @param array $weather
     *
     * @return string
     */
    private function getWeatherDescription(array $weather)
    {
        return $weather['weather'][0]['description'];
    }
}
