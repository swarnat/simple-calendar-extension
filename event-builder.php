<?php
namespace DTW\GoogleCalendarEventsExtension;

use SimpleCalendar\Abstracts\Calendar;
use SimpleCalendar\Calendars\Default_Calendar;
use SimpleCalendar\plugin_deps\Carbon\Carbon;

class EventBuilder {
    private $calendar;
    
    private static $CalendarCache = [];


    /**
     * set calendar instance
     *
     * @param int|Calendar $calendar
     * @return void
     */
    public function setCalendar($calendar) {
        
        if(is_numeric($calendar)) {
            if(!isset(self::$CalendarCache[$calendar])) {
                self::$CalendarCache[$calendar] = new Default_Calendar($calendar);
            }
            
            $this->calendar = self::$CalendarCache[$calendar];
            
        } elseif($calendar instanceof Calendar) {
            $this->calendar = $calendar;
        }
        
     
    }
    /**
     * @param \SimpleCalendar\Events\Event $event
     * @param mixed $attr
     * @return void
     */    
    public function generate($tag, \SimpleCalendar\Events\Event $event, $attr) {

		switch($tag) {
			case "eventbox_dates":
                return $this->eventbox_dates($event, $attr);
                break;
			case "eventbox_location":
                
                return $this->eventbox_location($event, $attr);
                break;
			case "image_diashows":
				return $this->image_diashows($event, $attr);
			break;
			case "eventbox_time":
				return $this->eventbox_time($event, $attr);
			break;
			/*
			case "description_nl2br":
				return kircheHohndorfDescriptionFormat($event, $attr);
			break;
			*/
            default;
                return $tag;
		}

    }

    private function eventbox_time($event, $attr) {
        if($event->whole_day) {
            if(!empty($attr["placeholder"])) {
                return "<br/>";
            }

            return "";
        }

        $startTime = date_i18n($this->calendar->time_format, strtotime($event->start_dt->toDateTimeString()));
            
        return "<strong>Zeit:</strong> " . $startTime . " Uhr<br />";

    }


    private function eventbox_location($event, $attr) {
        $location = $event->end_location;
    
        if(!empty($location) && !empty($location["address"])) {
            return "<strong>Ort:</strong> " . $location["address"] . "<br />";
        } else {
            
            if(!empty($attr["placeholder"])) {
                return "<br/>";
            }
            
            return "";
        }
        

    }

    private function eventbox_dates($event, $attr) {
        $startDate = $event->start_dt->format("d.m.");
        $endDate = $event->end_dt->format("d.m.");
    
        if($startDate != $endDate) {
            return "<div class='event-datum-container'>" . $startDate . " - " . $endDate . "</div>";
        } else {
            return "<div class='event-datum-container'>" . $startDate . "</div>";
        }        
    }

    private function image_diashows($event, $attr) {
        $attachments = $event->get_attachments();
	
        $html = '<ul class="simcal-attachments">' . "\n\t";
        $attr = array_merge(
                [
                        'view' => "list",
                        "width" => 250
                        // Convert url to link anchor
                ],
                (array) shortcode_parse_atts($attr)
        );
    
        $imgMime = ["image/jpeg", "image/jpg", "image/png"];
    
        $maxWidth = 250;        
        // $width = $attr["height"];
        foreach ($attachments as $attachment) {
            $html .= '<li class="simcal-attachment">';
            $view = $attr["view"];
    
            if(in_array($attachment["mime"], $imgMime) === false) {
                continue;
            }
    
            $imageData = parse_url($attachment["url"]);
    
            if(!empty($imageData["query"])) {
                $imageAttr = [];
                parse_str($imageData["query"], $imageAttr);
            }

            if(!empty($imageAttr["id"])) {
                                        $html .= '<a href="' . $attachment['url'] . '" target="_blank">';
                
                // if(!empty($attr["img-height"])) {
                    // $size = "width:auto;height:" . $height . "px;max-width:300px; max-height:" . $height . "px;";
                // } else {
                    $size = "height:auto;max-height:100%; max-width:" . $maxWidth . "px;";
                // }
                
                $html .= '<img src="https://drive.google.com/thumbnail?id=' . $imageAttr["id"] . '&sz=w1000" style="margin:0;'.$size.'" />';
                $html .= '</a>';
                
            } else {
                                    $html .= '<a href="' . $attachment['url'] . '" target="_blank">';
                                    $html .= !empty($attachment['icon']) ? '<img src="' . $attachment['icon'] . '" />' : '';
                                    $html .= '<span>' . $attachment['name'] . '</span>';
                                    $html .= '</a>';
            }
    
            $html .= '</li>' . "\n";
        }
            
        $html .= "</ul>";
        return $html;			
    }


}