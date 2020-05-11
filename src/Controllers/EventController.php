<?php


namespace greppy\Controllers;


use greppy\Contracts\ControllerInterface;
use greppy\Entity\Event;
use greppy\Http\Request;
use greppy\Http\Response;
use greppy\Http\Stream;
use greppy\Repository\EventRepository;

class EventController implements ControllerInterface
{
    /**
     * @var EventRepository
     */
    private $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @param Request $request
     * @param array $requestAttributes
     * @return Response
     */
    public function getEvents(Request $request, array $requestAttributes): Response
    {
        $filtersArray = array();
        ($request->getParameter("dateFrom")) ? $filtersArray["dateFrom"] = $request->getParameter("dateFrom") . (" " . $request->getParameter("hourFrom") ?? '') : '';
        ($request->getParameter("dateTo")) ? $filtersArray["dateTo"] = $request->getParameter("dateTo") . (" " . $request->getParameter("hourTo") ?? '') : '';

        $sortsArray = array();
        ($request->getParameter("sort")) ? $sortsArray[explode(":", $request->getParameter("sort"))[0]] = explode(":", $request->getParameter("sort"))[1] : '';

        $events = $this->eventRepository->findBy($filtersArray, $sortsArray);

        $eventsJson['data'] = array();
        foreach ($events as $event) {
            $eventJSON = array(
                'eventId' => $event->getId(),
                'description' => $event->getDescription(),
                'date' => $event->getDate(),
                'location' => $event->getLocation(),
                'userId' => $event->getUserId()
            );
            array_push($eventsJson['data'], $eventJSON);
        }
        return new Response
        (
            Stream::createFromString(json_encode($eventsJson)),
            ["Content-Type" => "application/json"]
        );
    }

    /**
     * @param Request $request
     * @param array $requestAttributes
     * @return Response
     */
    public function createEvent(Request $request, array $requestAttributes)
    {
        $eventAttr = json_decode($request->getBody()->__toString());

        $event = new Event();
        $event->setDescription($eventAttr->description);
        $event->setDate($eventAttr->date);
        $event->setLocation($eventAttr->location);
        $event->setUserId($eventAttr->userId);

        if (!$this->eventRepository->insertOnDuplicateKeyUpdate($event)) {
            return new Response(
                Stream::createFromString(json_encode(["message" => "Event Not Created"])),
                [
                    "Content-Type" => "application/json"
                ]
            );
        }

        return new Response
        (
            Stream::createFromString(json_encode(["message" => "Event Create Successfully"])),
            ["Content-Type" => "application/json"]
        );
    }

    /**
     * @param Request $request
     * @param array $requestAttributes
     * @return Response
     */
    public function deleteEvent(Request $request, array $requestAttributes)
    {
        $event = $this->eventRepository->find($requestAttributes['id']);

        if (!$this->eventRepository->delete($event)) {
            return new Response(
                Stream::createFromString(json_encode(["message" => "Event Not Deleted"])),
                ["Content-Type" => "application/json"]
            );
        }

        return new Response
        (
            Stream::createFromString(json_encode(["message" => "Event Delete Successfully"])),
            ["Content-Type" => "application/json"]
        );
    }

    /**
     * @param Request $request
     * @param array $requestAttributes
     * @return Response
     */
    public function updateEvent(Request $request, array $requestAttributes)
    {
        $eventAttr = json_decode($request->getBody()->__toString());

        $event = $this->eventRepository->find($requestAttributes['id']);
        $event->setDescription($eventAttr->description);
        $event->setDate($eventAttr->date);
        $event->setLocation($eventAttr->location);
        $event->setUserId($eventAttr->userId);

        if (!$this->eventRepository->insertOnDuplicateKeyUpdate($event)) {
            return new Response(
                Stream::createFromString(json_encode(["message" => "Event Not Updated"])),
                ["Content-Type" => "application/json"]
            );
        }

        return new Response
        (
            Stream::createFromString(json_encode(["message" => "Event Update Successfully"])),
            ["Content-Type" => "application/json"]
        );
    }
}